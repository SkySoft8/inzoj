<?php

namespace App\Services;

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Support\Collection;

class RecommendationEngine
{
    private const SEVERITY_RANK = [
        'critical' => 3,
        'warning' => 2,
        'info' => 1,
    ];

    /**
     * Evaluates the recommendation table against precomputed period contexts.
     *
     * @param  Collection<int, Recommendation>  $rules
     * @param  array<string, array>  $contexts  keyed by day/week/month/period
     */
    public static function evaluate(Collection $rules, User $user, array $contexts): array
    {
        $cards = [];

        foreach ($rules as $rule) {
            if (! $rule->is_active) {
                continue;
            }
            if ($rule->goal && $rule->goal !== $user->goal) {
                continue;
            }

            $context = $contexts[$rule->period] ?? null;
            if (! $context) {
                continue;
            }
            if ($context['tracked_days'] < (int) $rule->min_tracked_days) {
                continue;
            }

            $value = self::metricValue($rule, $context);
            if ($value === null) {
                continue;
            }

            if ($rule->metric === 'nutrient_percent' && ! self::coverageIsEnough($rule, $context)) {
                continue;
            }

            if (! self::matches($value, $rule)) {
                continue;
            }

            $nutrient = $rule->nutrient_code
                ? ($context['nutrients'][$rule->nutrient_code] ?? null)
                : null;

            $cards[] = [
                'code' => $rule->code,
                'title' => $rule->title,
                'text' => self::render($rule, $value, $context, $nutrient),
                'severity' => $rule->severity,
                'period' => $rule->period,
                'metric' => $rule->metric,
                'nutrient_code' => $rule->nutrient_code,
                'value' => self::number($value, $rule->value_unit),
                'threshold' => $rule->threshold !== null ? self::number($rule->threshold, $rule->value_unit) : null,
                'threshold_max' => $rule->threshold_max !== null ? self::number($rule->threshold_max, $rule->value_unit) : null,
                'unit' => $rule->value_unit,
                'priority' => (int) $rule->priority,
                'eaten' => $nutrient['eaten'] ?? null,
                'goal' => $nutrient['goal'] ?? null,
                'percent' => $nutrient['percent'] ?? null,
                'coverage' => $nutrient['coverage'] ?? null,
            ];
        }

        usort($cards, function (array $a, array $b) {
            $rank = (self::SEVERITY_RANK[$b['severity']] ?? 0) <=> (self::SEVERITY_RANK[$a['severity']] ?? 0);

            return $rank !== 0 ? $rank : ($b['priority'] <=> $a['priority']);
        });

        return $cards;
    }

    private static function metricValue(Recommendation $rule, array $context): ?float
    {
        return match ($rule->metric) {
            'nutrient_percent' => $context['nutrients'][$rule->nutrient_code]['percent'] ?? null,
            'water_percent' => $context['water_percent'] ?? null,
            'steps_percent' => $context['steps_percent'] ?? null,
            'weight_change' => $context['weight_change'] ?? null,
            'logged_days' => (float) ($context['logged_days'] ?? 0),
            'weight_logs' => (float) ($context['weight_logs'] ?? 0),
            default => null,
        };
    }

    private static function coverageIsEnough(Recommendation $rule, array $context): bool
    {
        if ((float) $rule->min_coverage <= 0) {
            return true;
        }

        $nutrient = $context['nutrients'][$rule->nutrient_code] ?? null;
        if (! $nutrient || ! ($nutrient['tracked'] ?? false)) {
            return false;
        }

        return (float) ($nutrient['coverage'] ?? 0) >= (float) $rule->min_coverage;
    }

    private static function matches(float $value, Recommendation $rule): bool
    {
        $threshold = $rule->threshold;
        if ($threshold === null) {
            return false;
        }

        return match ($rule->comparison) {
            'lt' => $value < $threshold,
            'lte' => $value <= $threshold,
            'gt' => $value > $threshold,
            'gte' => $value >= $threshold,
            'between' => $rule->threshold_max !== null
                && $value >= $threshold
                && $value <= $rule->threshold_max,
            default => false,
        };
    }

    private static function render(Recommendation $rule, float $value, array $context, ?array $nutrient): string
    {
        return str_replace(
            [':value', ':threshold', ':threshold_max', ':nutrient', ':days'],
            [
                self::format($value, $rule->value_unit),
                $rule->threshold !== null ? self::format($rule->threshold, $rule->value_unit) : '',
                $rule->threshold_max !== null ? self::format($rule->threshold_max, $rule->value_unit) : '',
                $nutrient['label'] ?? '',
                (string) $context['tracked_days'],
            ],
            $rule->text
        );
    }

    private static function format(float $value, ?string $unit): string
    {
        if ($unit === '%') {
            return (string) (int) round($value);
        }
        if ($unit === 'кг') {
            return number_format($value, 1, '.', '');
        }
        if (in_array($unit, ['дн.', 'шт.'], true)) {
            return (string) (int) round($value);
        }

        return number_format($value, 1, '.', '');
    }

    private static function number(float $value, ?string $unit): float
    {
        if (in_array($unit, ['%', 'дн.', 'шт.'], true)) {
            return round($value);
        }

        return round($value, 1);
    }
}
