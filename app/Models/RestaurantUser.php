<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class RestaurantUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'restaurant_id'
    ];

    protected $hidden = [
        'password',
    ];

    public function restaurant()
    {
        return $this->belongsTo(\App\Models\Restaurants\Restaurant::class);
    }
}
