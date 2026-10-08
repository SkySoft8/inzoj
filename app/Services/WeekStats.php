<?php

namespace App\Services;

use App\Models\BodyLog;
use App\Models\DiaryNote;
use App\Models\User;
use Illuminate\Support\Carbon;

class WeekStats
{
    private const SHORTAGE_PERCENT = 80;

    public static function build(User $user, Carbon $from, Carbon $to): array
    {
        $from = $from->copy()->startOfDay();
        $to = $to->copy()->startOfDay();
        $days = self::days($user, $from, $to);
        $count = max(count($days), 1);

        $caloriesGoal = self::goal($user->calories);
        $proteinGoal = self::goal($user->proteins);
        $fatGoal = self::goal($user->fats);
        $carbGoal = self::goal($user->carbs);

        $caloriesEaten = round(collect($days)->sum('calories') / $count, 1);
        $proteinEaten = round(collect($days)->sum('proteins') / $count, 1);
        $fatEaten = round(collect($days)->sum('fats') / $count, 1);
        $carbEaten = round(collect($days)->sum('carbs') / $count, 1);

        $catalog = FoodRecommendations::forUser($user->food_preferences, $user->allergies);

        $tracked = [
            self::nutrient('calories', 'Калории', 'ккал', $caloriesEaten, $caloriesGoal, true),
            self::nutrient('proteins', 'Белки', 'г', $proteinEaten, $proteinGoal, true),
            self::nutrient('fats', 'Жиры', 'г', $fatEaten, $fatGoal, true),
            self::nutrient('carbs', 'Углеводы', 'г', $carbEaten, $carbGoal, true),
        ];

        $attention = [];
        foreach ($tracked as $item) {
            if ($item['percent'] === null || $item['percent'] >= self::SHORTAGE_PERCENT) {
                continue;
            }
            $attention[] = self::attention($item, $catalog);
        }

        $weight = BodyLog::where('user_id', $user->id)
            ->where('type', BodyLog::TYPE_WEIGHT)
            ->whereDate('logged_at', '>=', $from->toDateString())
            ->whereDate('logged_at', '<=', $to->toDateString())
            ->orderBy('logged_at')
            ->get()
            ->map(function (BodyLog $log) {
                return [
                    'date' => $log->logged_at->toDateString(),
                    'value' => (float) $log->value,
                ];
            })
            ->values()
            ->all();

        return [
            'success' => true,
            'period' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'label' => $from->format('d.m').'–'.$to->format('d.m'),
            ],
            'calories' => [
                'label' => 'Калории',
                'eaten' => $caloriesEaten,
                'goal' => $caloriesGoal,
                'percent' => self::percent($caloriesEaten, $caloriesGoal),
                'unit' => 'ккал',
                'days' => array_map(function (array $day) use ($caloriesGoal) {
                    return [
                        'date' => $day['date'],
                        'value' => $day['calories'],
                        'goal' => $caloriesGoal,
                    ];
                }, $days),
            ],
            'bju' => [
                self::nutrient('proteins', 'Белки', 'г', $proteinEaten, $proteinGoal, true),
                self::nutrient('fats', 'Жиры', 'г', $fatEaten, $fatGoal, true),
                self::nutrient('carbs', 'Углеводы', 'г', $carbEaten, $carbGoal, true),
            ],
            'weight' => [
                'unit' => 'кг',
                'points' => $weight,
            ],
            'nutrients' => [
                'main' => array_merge($tracked, [
                    self::nutrient('fiber', 'Клетчатка', 'г', null, null, false),
                ]),
                'minerals' => [
                    self::nutrient('calcium', 'Кальций', 'мг', null, null, false),
                    self::nutrient('magnesium', 'Магний', 'мг', null, null, false),
                    self::nutrient('iron', 'Железо', 'мг', null, null, false),
                    self::nutrient('potassium', 'Калий', 'мг', null, null, false),
                    self::nutrient('sodium', 'Натрий', 'мг', null, null, false),
                ],
                'vitamins' => [
                    self::nutrient('b1', 'Витамин B1', 'мг', null, null, false),
                    self::nutrient('b6', 'Витамин B6', 'мг', null, null, false),
                    self::nutrient('b9', 'Витамин B9', 'мкг', null, null, false),
                    self::nutrient('b12', 'Витамин B12', 'мкг', null, null, false),
                    self::nutrient('c', 'Витамин C', 'мг', null, null, false),
                    self::nutrient('d', 'Витамин D', 'мкг', null, null, false),
                ],
            ],
            'attention' => $attention,
        ];
    }

    private static function days(User $user, Carbon $from, Carbon $to): array
    {
        $notes = DiaryNote::where('user_id', $user->id)
            ->whereDate('diary_date', '>=', $from->toDateString())
            ->whereDate('diary_date', '<=', $to->toDateString())
            ->get()
            ->keyBy(function (DiaryNote $note) {
                return Carbon::parse($note->diary_date)->toDateString();
            });

        $days = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $note = $notes->get($key);
            $days[] = [
                'date' => $key,
                'calories' => $note ? (float) $note->current_calories : 0.0,
                'proteins' => $note ? (float) $note->current_proteins : 0.0,
                'fats' => $note ? (float) $note->current_fats : 0.0,
                'carbs' => $note ? (float) $note->current_carbs : 0.0,
            ];
            $cursor->addDay();
        }

        return $days;
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

    private static function nutrient(string $code, string $label, string $unit, ?float $eaten, ?float $goal, bool $tracked): array
    {
        return [
            'code' => $code,
            'label' => $label,
            'eaten' => $eaten,
            'goal' => $goal,
            'percent' => $tracked ? self::percent($eaten, $goal) : null,
            'unit' => $unit,
            'tracked' => $tracked,
        ];
    }

    private static function attention(array $item, array $catalog): array
    {
        $copy = [
            'calories' => 'Калорий за эти дни меньше дневной цели. Добавьте полноценные приёмы пищи.',
            'proteins' => 'Белка за эти дни меньше дневной цели. Добавьте рыбу, мясо, творог или бобовые.',
            'fats' => 'Жиров за эти дни меньше дневной цели. Подойдут орехи, авокадо и растительное масло.',
            'carbs' => 'Углеводов за эти дни меньше дневной цели. Подойдут крупы и цельнозерновой хлеб.',
        ];

        return [
            'code' => $item['code'],
            'title' => self::title($item['code']),
            'text' => $copy[$item['code']] ?? '',
            'eaten' => $item['eaten'],
            'goal' => $item['goal'],
            'percent' => $item['percent'],
            'unit' => $item['unit'],
            'products' => self::products($item['code'], $catalog),
        ];
    }

    private static function title(string $code): string
    {
        return match ($code) {
            'proteins' => 'Мало белка',
            'fats' => 'Мало жиров',
            'carbs' => 'Мало углеводов',
            default => 'Мало калорий',
        };
    }

    private static function products(string $code, array $catalog): array
    {
        $needles = match ($code) {
            'proteins' => ['белок'],
            'fats' => ['жир'],
            'carbs' => ['углевод'],
            default => [],
        };

        $picked = [];
        foreach ($catalog as $item) {
            $role = mb_strtolower((string) ($item['role'] ?? ''));
            $matches = $needles === [];
            foreach ($needles as $needle) {
                if (str_contains($role, $needle)) {
                    $matches = true;
                }
            }
            if (!$matches) {
                continue;
            }
            $picked[] = [
                'name' => $item['name'],
                'role' => $item['role'],
                'calories' => $item['calories'],
                'proteins' => $item['proteins'],
                'fats' => $item['fats'],
                'carbs' => $item['carbs'],
            ];
            if (count($picked) >= 4) {
                break;
            }
        }

        return $picked;
    }
}
