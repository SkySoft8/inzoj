<?php

namespace App\Models\Restaurants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
        'price',
        'weight',
        'ingredients',
        'calories',
        'proteins',
        'fats',
        'carbs'
    ];
}
