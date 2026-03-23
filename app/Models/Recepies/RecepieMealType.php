<?php

namespace App\Models\Recepies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecepieMealType extends Model
{
    use HasFactory;

    protected $fillable = [
        'recepie_id',
        'meal_type'
    ];
}
