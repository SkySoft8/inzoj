<?php

namespace Database\Seeders;

use App\Models\Nutrient;
use App\Models\NutrientNorm;
use Illuminate\Database\Seeder;

class NutrientSeeder extends Seeder
{
    private const SOURCE = 'МР 2.3.1.0253-21';

    public function run(): void
    {
        $nutrients = [
            ['code' => 'fiber', 'label' => 'Клетчатка', 'unit' => 'г', 'category' => 'main', 'sort_order' => 10],
            ['code' => 'calcium', 'label' => 'Кальций', 'unit' => 'мг', 'category' => 'mineral', 'sort_order' => 20],
            ['code' => 'magnesium', 'label' => 'Магний', 'unit' => 'мг', 'category' => 'mineral', 'sort_order' => 21],
            ['code' => 'iron', 'label' => 'Железо', 'unit' => 'мг', 'category' => 'mineral', 'sort_order' => 22],
            ['code' => 'potassium', 'label' => 'Калий', 'unit' => 'мг', 'category' => 'mineral', 'sort_order' => 23],
            ['code' => 'sodium', 'label' => 'Натрий', 'unit' => 'мг', 'category' => 'mineral', 'sort_order' => 24],
            ['code' => 'vitamin_b1', 'label' => 'Витамин B1', 'unit' => 'мг', 'category' => 'vitamin', 'sort_order' => 30],
            ['code' => 'vitamin_b6', 'label' => 'Витамин B6', 'unit' => 'мг', 'category' => 'vitamin', 'sort_order' => 31],
            ['code' => 'vitamin_b9', 'label' => 'Витамин B9', 'unit' => 'мкг', 'category' => 'vitamin', 'sort_order' => 32],
            ['code' => 'vitamin_b12', 'label' => 'Витамин B12', 'unit' => 'мкг', 'category' => 'vitamin', 'sort_order' => 33],
            ['code' => 'vitamin_c', 'label' => 'Витамин C', 'unit' => 'мг', 'category' => 'vitamin', 'sort_order' => 34],
            ['code' => 'vitamin_d', 'label' => 'Витамин D', 'unit' => 'мкг', 'category' => 'vitamin', 'sort_order' => 35],
        ];

        foreach ($nutrients as $nutrient) {
            Nutrient::updateOrCreate(['code' => $nutrient['code']], $nutrient);
        }

        $norms = [
            ['nutrient_code' => 'fiber', 'amount' => 25],
            ['nutrient_code' => 'calcium', 'amount' => 1000],
            ['nutrient_code' => 'calcium', 'age_min' => 60, 'amount' => 1200],
            ['nutrient_code' => 'magnesium', 'amount' => 400],
            ['nutrient_code' => 'magnesium', 'gender' => 'male', 'amount' => 420],
            ['nutrient_code' => 'magnesium', 'gender' => 'female', 'amount' => 320],
            ['nutrient_code' => 'iron', 'amount' => 14],
            ['nutrient_code' => 'iron', 'gender' => 'male', 'amount' => 10],
            ['nutrient_code' => 'iron', 'gender' => 'female', 'amount' => 18],
            ['nutrient_code' => 'potassium', 'amount' => 3500],
            ['nutrient_code' => 'sodium', 'amount' => 2000],
            ['nutrient_code' => 'vitamin_b1', 'amount' => 1.5],
            ['nutrient_code' => 'vitamin_b6', 'amount' => 2],
            ['nutrient_code' => 'vitamin_b9', 'amount' => 400],
            ['nutrient_code' => 'vitamin_b12', 'amount' => 3],
            ['nutrient_code' => 'vitamin_c', 'amount' => 90],
            ['nutrient_code' => 'vitamin_c', 'gender' => 'female', 'amount' => 75],
            ['nutrient_code' => 'vitamin_d', 'amount' => 15],
        ];

        foreach ($norms as $norm) {
            NutrientNorm::updateOrCreate(
                [
                    'nutrient_code' => $norm['nutrient_code'],
                    'gender' => $norm['gender'] ?? null,
                    'age_min' => $norm['age_min'] ?? null,
                    'age_max' => $norm['age_max'] ?? null,
                ],
                [
                    'amount' => $norm['amount'],
                    'source' => self::SOURCE,
                ]
            );
        }
    }
}
