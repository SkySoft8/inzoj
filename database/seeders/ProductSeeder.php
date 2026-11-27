<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->items() as $item) {
            Product::updateOrCreate(['name' => $item['name']], $item);
        }
    }

    private function items(): array
    {
        return [
            $this->item('Овсянка', '4600000000001', 379, 13.2, 6.5, 67.5, [
                'fiber' => 10.6, 'calcium' => 54, 'magnesium' => 177, 'iron' => 4.7,
                'potassium' => 429, 'sodium' => 6, 'vitamin_b1' => 0.76, 'vitamin_b6' => 0.12,
                'vitamin_b9' => 56, 'vitamin_b12' => 0, 'vitamin_c' => 0, 'vitamin_d' => 0,
            ]),
            $this->item('Яйцо', '4600000000002', 155, 12.6, 10.6, 1.1, [
                'fiber' => 0, 'calcium' => 56, 'magnesium' => 12, 'iron' => 1.75,
                'potassium' => 138, 'sodium' => 142, 'vitamin_b1' => 0.04, 'vitamin_b6' => 0.17,
                'vitamin_b9' => 47, 'vitamin_b12' => 0.89, 'vitamin_c' => 0, 'vitamin_d' => 2,
            ]),
            $this->item('Морковь', '4600000000003', 41, 0.9, 0.2, 9.6, [
                'fiber' => 2.8, 'calcium' => 33, 'magnesium' => 12, 'iron' => 0.3,
                'potassium' => 320, 'sodium' => 69, 'vitamin_b1' => 0.066, 'vitamin_b6' => 0.138,
                'vitamin_b9' => 19, 'vitamin_b12' => 0, 'vitamin_c' => 5.9, 'vitamin_d' => 0,
            ]),
        ];
    }

    private function item(
        string $name,
        string $shortcode,
        float $calories,
        float $proteins,
        float $fats,
        float $carbs,
        array $nutrients
    ): array {
        return [
            'name' => $name,
            'shortcode' => $shortcode,
            'calories' => $calories,
            'proteins' => $proteins,
            'fats' => $fats,
            'carbs' => $carbs,
            'nutrients' => $nutrients,
        ];
    }
}
