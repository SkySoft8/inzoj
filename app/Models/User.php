<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'goal',
        'current_weight',
        'target_weight',
        'height',
        'age',
        'gender',
        'activity_level',
        'calories',
        'proteins',
        'fats',
        'carbs',
        'water',
        'steps',
        'food_preferences',
        'allergies',
        'profile_photo_path',
        'is_premium',
        'premium_until'
    ];

    protected $hidden = [
        'password',
        'profile_photo_path',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'premium_until' => 'datetime',
        'proteins' => 'float',
        'fats' => 'float',
        'carbs' => 'float',
        'water' => 'float',
    ];

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!$this->profile_photo_path) {
            return null;
        }

        return request()->is('api/*')
            ? url('/api/profile/photo/file')
            : route('profile.photo.file');
    }

    public function isPremiumActive(): bool
    {
        return (bool) $this->is_premium
            && $this->premium_until
            && $this->premium_until->isFuture();
    }

    public function bodyLogs()
    {
        return $this->hasMany(BodyLog::class);
    }

    public function progressPhotos()
    {
        return $this->hasMany(ProgressPhoto::class);
    }
}