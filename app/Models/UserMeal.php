<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'diary_note_id',
        'product_id',
        'recepie_id',
        'dish_id',
        'meal_type',
        'amount'
    ];
    
}
