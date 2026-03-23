<?php

namespace App\Models\Recepies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recepie extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'instructions',
        'image',
        'calories',
        'proteins',
        'fats',
        'carbs'
    ];

}
