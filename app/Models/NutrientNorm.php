<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutrientNorm extends Model
{
    protected $fillable = [
        'nutrient_code',
        'gender',
        'age_min',
        'age_max',
        'amount',
        'source',
    ];

    protected $casts = [
        'age_min' => 'integer',
        'age_max' => 'integer',
        'amount' => 'float',
    ];
}
