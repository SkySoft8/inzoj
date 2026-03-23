<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiaryNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'diary_date',
        'current_calories',
        'burned_calories',
        'current_proteins',
        'current_fats',
        'current_carbs',
        'current_water',
        'current_steps',
    ];

}
