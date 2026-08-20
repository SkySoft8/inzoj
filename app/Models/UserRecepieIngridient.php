<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRecepieIngridient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_recepie_id',
        'ingridient_id',
    ];
}
