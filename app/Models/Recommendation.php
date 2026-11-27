<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    protected $fillable = [
        'code',
        'title',
        'text',
        'severity',
        'metric',
        'nutrient_code',
        'goal',
        'comparison',
        'threshold',
        'threshold_max',
        'value_unit',
        'period',
        'min_tracked_days',
        'min_coverage',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'threshold' => 'float',
        'threshold_max' => 'float',
        'min_coverage' => 'float',
        'min_tracked_days' => 'integer',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];
}
