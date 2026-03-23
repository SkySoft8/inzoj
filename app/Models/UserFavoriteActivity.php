<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFavoriteActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_id'
    ];
}
