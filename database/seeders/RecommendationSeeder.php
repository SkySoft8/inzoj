<?php

namespace Database\Seeders;

use App\Models\Recommendation;
use Illuminate\Database\Seeder;

class RecommendationSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->rules() as $rule) {
            Recommendation::updateOrCreate(['code' => $rule['code']], $rule);
        }
    }

    private function rules(): array
    {
        return [
            [
                'code' => 'calories_low',
                'title' => 'Мало калорий',
                'text' => 'Средняя калорийность за неделю — :value% от цели. Длительный дефицит снижает энергию и замедляет обмен: добавьте полноценный приём пищи или питательный перекус.',
                'severity' => 'warning',
                'metric' => 'nutrient_percent',
                'nutrient_code' => 'calories',
                'comparison' => 'lt',
                'threshold' => 80,
                'value_unit' => '%',
                'period' => 'week',
                'min_tracked_days' => 4,
                'priority' => 100,
            ],
            [
                'code' => 'protein_low',
                'title' => 'Мало белка',
                'text' => 'Белок закрыт на :value% от дневной цели. Белок поддерживает мышцы и насыщение — распределите его на все приёмы пищи.',
                'severity' => 'warning',
                'metric' => 'nutrient_percent',
                'nutrient_code' => 'proteins',
                'comparison' => 'lt',
                'threshold' => 80,
                'value_unit' => '%',
                'period' => 'week',
                'min_tracked_days' => 4,
                'priority' => 90,
            ],
            [
                'code' => 'fats_low',
                'title' => 'Мало жиров',
                'text' => 'Жиры закрыты на :value% от цели. Жиры нужны для гормонов и усвоения витаминов — не опускайте их ниже разумного минимума.',
                'severity' => 'warning',
                'metric' => 'nutrient_percent',
                'nutrient_code' => 'fats',
                'comparison' => 'lt',
                'threshold' => 70,
                'value_unit' => '%',
                'period' => 'week',
                'min_tracked_days' => 4,
                'priority' => 80,
            ],
            [
                'code' => 'carbs_low',
                'title' => 'Мало углеводов',
                'text' => 'Углеводы закрыты на :value% от цели. Если самочувствие и тренировки страдают, добавьте сложные углеводы.',
                'severity' => 'info',
                'metric' => 'nutrient_percent',
                'nutrient_code' => 'carbs',
                'comparison' => 'lt',
                'threshold' => 70,
                'value_unit' => '%',
                'period' => 'week',
                'min_tracked_days' => 4,
                'priority' => 70,
            ],
            [
                'code' => 'steps_low_3000',
                'title' => 'Шаги: проверка рекомендаций',
                'text' => 'Шаги закрыты на :value% от цели. Это тестовая рекомендация, чтобы проверить работу правил.',
                'severity' => 'info',
                'metric' => 'steps_percent',
                'comparison' => 'gte',
                'threshold' => 0,
                'value_unit' => '%',
                'period' => 'period',
                'min_tracked_days' => 1,
                'priority' => 75,
            ],
            [
                'code' => 'vitamin_c_low',
                'title' => 'Мало витамина C',
                'text' => 'Витамин C закрыт на :value% от нормы. Добавьте свежие овощи, фрукты и ягоды.',
                'severity' => 'warning',
                'metric' => 'nutrient_percent',
                'nutrient_code' => 'vitamin_c',
                'comparison' => 'lt',
                'threshold' => 70,
                'value_unit' => '%',
                'period' => 'month',
                'min_tracked_days' => 7,
                'min_coverage' => 50,
                'priority' => 60,
            ],
        ];
    }
}
