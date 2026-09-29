<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFavoriteUserRecepie extends Model
{
    protected $fillable = [
        'user_id',
        'user_recepie_id',
    ];
}
