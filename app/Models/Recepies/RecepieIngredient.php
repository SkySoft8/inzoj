<?php

namespace App\Models\Recepies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecepieIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'recepie_id',
        'name',
        'amount'
    ];

}
