<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nutrient extends Model
{
    protected $fillable = [
        'code',
        'label',
        'unit',
        'category',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
