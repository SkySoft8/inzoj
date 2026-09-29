<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Тыква', 'calories' => 26, 'proteins' => 1.0, 'fats' => 0.1, 'carbs' => 6.5],
            ['name' => 'Лук репчатый', 'calories' => 41, 'proteins' => 1.4, 'fats' => 0.2, 'carbs' => 9.0],
            ['name' => 'Сливки 10%', 'calories' => 119, 'proteins' => 2.7, 'fats' => 10.0, 'carbs' => 4.5],
            ['name' => 'Соль', 'calories' => 0, 'proteins' => 0, 'fats' => 0, 'carbs' => 0],
            ['name' => 'Сахар', 'calories' => 399, 'proteins' => 0, 'fats' => 0, 'carbs' => 99.8],
            ['name' => 'Молоко', 'calories' => 60, 'proteins' => 3.2, 'fats' => 3.2, 'carbs' => 4.7],
            ['name' => 'Мука', 'calories' => 342, 'proteins' => 10.3, 'fats' => 1.1, 'carbs' => 72.0],
            ['name' => 'Картофель', 'calories' => 77, 'proteins' => 2.0, 'fats' => 0.4, 'carbs' => 16.3],
            ['name' => 'Морковь', 'calories' => 41, 'proteins' => 0.9, 'fats' => 0.2, 'carbs' => 9.6],
            ['name' => 'Куриная грудка', 'calories' => 165, 'proteins' => 31.0, 'fats' => 3.6, 'carbs' => 0],
            ['name' => 'Рис', 'calories' => 344, 'proteins' => 6.7, 'fats' => 0.7, 'carbs' => 78.9],
            ['name' => 'Овсянка', 'calories' => 379, 'proteins' => 13.2, 'fats' => 6.5, 'carbs' => 67.5],
            ['name' => 'Яйцо', 'calories' => 155, 'proteins' => 12.6, 'fats' => 10.6, 'carbs' => 1.1],
            ['name' => 'Масло оливковое', 'calories' => 898, 'proteins' => 0, 'fats' => 99.8, 'carbs' => 0],
            ['name' => 'Ананас', 'calories' => 52, 'proteins' => 0.4, 'fats' => 0.2, 'carbs' => 13.1],
        ];

        foreach ($items as $item) {
            Product::firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
