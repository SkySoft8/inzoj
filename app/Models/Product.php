<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'calories',
        'proteins',
        'fats',
        'carbs',
        'serving_label',
        'serving_grams',
        'serving_unit',
    ];

    protected $appends = [
        'units',
    ];

    public function getUnitsAttribute(): array
    {
        $units = [
            ['unit' => 'g', 'label' => 'г'],
        ];

        if (in_array($this->serving_unit, ['pcs', 'ml'], true) && (int) $this->serving_grams > 0) {
            $units[] = [
                'unit' => $this->serving_unit,
                'label' => $this->serving_unit === 'pcs' ? 'шт' : 'мл',
                'grams' => (int) $this->serving_grams,
            ];
        }

        return $units;
    }

    public function gramsForPortion(int $quantity, string $unit): int
    {
        if ($unit === 'g') {
            return $quantity;
        }

        if ($this->serving_unit !== $unit || (int) $this->serving_grams < 1) {
            throw new \InvalidArgumentException('This product has no such portion unit');
        }

        return $quantity * (int) $this->serving_grams;
    }
}
