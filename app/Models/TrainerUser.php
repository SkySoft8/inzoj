<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TrainerUser extends Authenticatable
{
    use HasFactory, HasApiTokens;
    
    protected $table = 'trainer_users';
    
    protected $fillable = [
        'email',
        'password',
        'name', 
        'surname',
        'birthday',
        'experience',
        'achievements',
        'rating',
        'rating_count',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'birthday' => 'date'
    ];

}
