<?php

namespace App\Services;

use App\Models\BodyLog;
use App\Models\DiaryNote;
use App\Models\Nutrient;
use App\Models\NutrientNorm;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PeriodStats
{
    public const MAX_PERIOD_DAYS = 366;

    private const WINDOWS = [
        'day' => 1,
        'week' => 7,
        'month' => 30,
    ];

    public static function build(User $user, Carbon $from, Carbon $to): array
    {
        $from = $from->copy()->startOfDay();
        $to = $to->copy()->startOfDay();

        $nutrients = Nutrient::where('is_active', true)->orderBy('sort_order')->get();
        $norms = NutrientNorm::whereIn('nutrient_code', $nutrients->pluck('code'))->get();
        $rules = Recommendation::where('is_active', true)->orderByDesc('priority')->get();

        $windows = ['period' => [$from->copy(), $to->copy()]];
        foreach ($rules->pluck('period')->unique() as $name) {
            if ($name !== 'period' && isset(self::WINDOWS[$name])) {
                $windows[$name] = [
                    $to->copy()->subDays(self::WINDOWS[$name] - 1)->startOfDay(),
                    $to->copy(),
                ];
            }
        }

        $contexts = [];
        foreach ($windows as $name => [$start, $end]) {
            $contexts[$name] = self::context($user, $nutrients, $norms, $start, $end);
        }

        $context = $contexts['period'];
        $recommendations = RecommendationEngine::evaluate($rules, $user, $contexts);

        $bju = [
            $context['macro']['proteins'],
            $context['macro']['fats'],
            $context['macro']['carbs'],
        ];

        return [
            'success' => true,
            'period' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'label' => $from->format('d.m').'–'.$to->format('d.m'),
                'days' => $context['days_count'],
                'tracked_days' => $context['tracked_days'],
            ],
            'calories' => [
                'label' => 'Калории',
                'eaten' => $context['macro']['calories']['eaten'],
                'goal' => $context['macro']['calories']['goal'],
                'percent' => $context['macro']['calories']['percent'],
                'unit' => 'ккал',
                'days' => array_map(function (array $day) use ($context) {
                    return [
                        'date' => $day['date'],
                        'value' => $day['calories'],
                        'goal' => $context['macro']['calories']['goal'],
                        'tracked' => $day['tracked'],
                    ];
                }, $context['days']),
            ],
            'bju' => $bju,
            'weight' => [
                'unit' => 'кг',
                'points' => $context['weight_points'],
                'start' => $context['weight_start'],
                'current' => $context['weight_end'],
                'change' => $context['weight_change'],
            ],
            'water' => [
                'label' => 'Вода',
                'unit' => 'л',
                'eaten' => $context['water_average'],
                'goal' => $context['water_goal'],
                'percent' => $context['water_percent'],
                'tracked' => $context['tracked_days'] > 0,
            ],
            'steps' => [
                'label' => 'Шаги',
                'unit' => 'шаг.',
                'eaten' => $context['steps_average'],
                'goal' => $context['steps_goal'],
                'percent' => $context['steps_percent'],
                'tracked' => $context['tracked_days'] > 0,
            ],
            'nutrients' => [
                'coverage_percent' => $context['coverage'],
                'has_data' => $context['coverage'] > 0,
                'main' => array_values(array_filter([
                    $context['macro']['calories'],
                    $context['macro']['proteins'],
                    $context['macro']['fats'],
                    $context['macro']['carbs'],
                    $context['nutrient_stats']['fiber'] ?? null,
                ])),
                'minerals' => array_values(array_filter(
                    $context['nutrient_stats'],
                    fn (array $item) => $item['category'] === 'mineral'
                )),
                'vitamins' => array_values(array_filter(
                    $context['nutrient_stats'],
                    fn (array $item) => $item['category'] === 'vitamin'
                )),
            ],
            'recommendations' => $recommendations,
        ];
    }

    private static function context(
        User $user,
        Collection $nutrients,
        Collection $norms,
        Carbon $from,
        Carbon $to
    ): array {
        $notes = DiaryNote::where('user_id', $user->id)
            ->whereDate('diary_date', '>=', $from->toDateString())
            ->whereDate('diary_date', '<=', $to->toDateString())
            ->get()
            ->keyBy(fn (DiaryNote $note) => Carbon::parse($note->diary_date)->toDateString());

        $days = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $note = $notes->get($key);
            $calories = $note ? (float) $note->current_calories : 0.0;
            $water = $note ? (float) $note->current_water : 0.0;
            $steps = $note ? (int) $note->current_steps : 0;
            $days[] = [
                'date' => $key,
                'calories' => $calories,
                'proteins' => $note ? (float) $note->current_proteins : 0.0,
                'fats' => $note ? (float) $note->current_fats : 0.0,
                'carbs' => $note ? (float) $note->current_carbs : 0.0,
                'water' => $water,
                'steps' => $steps,
                'tracked' => $calories > 0 || $water > 0 || $steps > 0,
            ];
            $cursor->addDay();
        }

        $trackedDays = count(array_filter($days, fn (array $day) => $day['tracked']));
        $divisor = max($trackedDays, 1);
        $sum = fn (string $key) => collect($days)->sum($key);
        $average = fn (string $key) => round($sum($key) / $divisor, 1);

        $goals = [
            'calories' => self::goal($user->calories),
            'proteins' => self::goal($user->proteins),
            'fats' => self::goal($user->fats),
            'carbs' => self::goal($user->carbs),
        ];

        $macro = [];
        foreach ([
            'calories' => ['Калории', 'ккал'],
            'proteins' => ['Белки', 'г'],
            'fats' => ['Жиры', 'г'],
            'carbs' => ['Углеводы', 'г'],
        ] as $code => [$label, $unit]) {
            $eaten = $average($code);
            $macro[$code] = [
                'code' => $code,
                'label' => $label,
                'category' => 'main',
                'eaten' => $trackedDays > 0 ? $eaten : 0.0,
                'goal' => $goals[$code],
                'percent' => $trackedDays > 0 ? self::percent($eaten, $goals[$code]) : null,
                'unit' => $unit,
                'tracked' => $trackedDays > 0,
                'estimated' => false,
                'coverage' => $trackedDays > 0 ? 100.0 : 0.0,
            ];
        }

        $codes = $nutrients->pluck('code')->all();
        $intake = NutrientIntake::forNotes($user->id, $notes->pluck('id')->all(), $codes);
        $coverage = $intake['total_grams'] > 0
            ? round($intake['known_grams'] / $intake['total_grams'] * 100, 1)
            : 0.0;

        $nutrientStats = [];
        foreach ($nutrients as $nutrient) {
            $total = (float) ($intake['totals'][$nutrient->code] ?? 0);
            $hasData = $intake['known_grams'] > 0;
            $eaten = $trackedDays > 0 ? round($total / $trackedDays, 2) : null;
            $goal = self::normFor($norms, $nutrient->code, $user);
            $nutrientStats[$nutrient->code] = [
                'code' => $nutrient->code,
                'label' => $nutrient->label,
                'category' => $nutrient->category,
                'eaten' => $hasData ? $eaten : null,
                'goal' => $goal,
                'percent' => ($hasData && $eaten !== null) ? self::percent($eaten, $goal) : null,
                'unit' => $nutrient->unit,
                'tracked' => $hasData,
                'estimated' => $hasData && $coverage < 95,
                'coverage' => $hasData ? $coverage : 0.0,
            ];
        }

        $water = $average('water');
        $steps = $sum('steps') / $divisor;

        $weightLogs = BodyLog::where('user_id', $user->id)
            ->where('type', BodyLog::TYPE_WEIGHT)
            ->whereDate('logged_at', '>=', $from->toDateString())
            ->whereDate('logged_at', '<=', $to->toDateString())
            ->orderBy('logged_at')
            ->get();

        $weightPoints = $weightLogs->map(fn (BodyLog $log) => [
            'date' => $log->logged_at->toDateString(),
            'value' => (float) $log->value,
        ])->values()->all();

        $weightStart = $weightLogs->first()?->value;
        $weightEnd = $weightLogs->last()?->value;
        $weightChange = ($weightLogs->count() >= 2)
            ? round((float) $weightEnd - (float) $weightStart, 2)
            : null;

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'days' => $days,
            'days_count' => count($days),
            'tracked_days' => $trackedDays,
            'macro' => $macro,
            'nutrients' => array_merge($macro, $nutrientStats),
            'nutrient_stats' => $nutrientStats,
            'coverage' => $coverage,
            'water_average' => $trackedDays > 0 ? round($water, 2) : 0.0,
            'water_goal' => self::goal($user->water),
            'water_percent' => $trackedDays > 0 ? self::percent($water, self::goal($user->water)) : null,
            'steps_average' => $trackedDays > 0 ? round($steps) : 0,
            'steps_goal' => self::goal($user->steps),
            'steps_percent' => $trackedDays > 0 ? self::percent($steps, self::goal($user->steps)) : null,
            'logged_days' => $trackedDays,
            'weight_points' => $weightPoints,
            'weight_start' => $weightStart !== null ? (float) $weightStart : null,
            'weight_end' => $weightEnd !== null ? (float) $weightEnd : null,
            'weight_change' => $weightChange,
            'weight_logs' => $weightLogs->count(),
        ];
    }

    private static function normFor(Collection $norms, string $code, User $user): ?float
    {
        $best = null;
        $bestScore = -1;
        $age = (int) $user->age;

        foreach ($norms->where('nutrient_code', $code) as $norm) {
            if ($norm->gender && $norm->gender !== $user->gender) {
                continue;
            }
            if ($norm->age_min !== null && $age < $norm->age_min) {
                continue;
            }
            if ($norm->age_max !== null && $age > $norm->age_max) {
                continue;
            }

            $score = ($norm->gender ? 2 : 0)
                + (($norm->age_min !== null || $norm->age_max !== null) ? 1 : 0);

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $norm;
            }
        }

        return $best ? (float) $best->amount : null;
    }

    private static function goal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 1);
    }

    private static function percent(?float $eaten, ?float $goal): ?int
    {
        if ($eaten === null || $goal === null || $goal <= 0) {
            return null;
        }

        return (int) round($eaten / $goal * 100);
    }
}
