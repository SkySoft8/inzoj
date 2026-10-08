<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    public const LOCATION_HOME = 'home';
    public const LOCATION_GYM = 'gym';
    public const LOCATION_OUTDOOR = 'outdoor';
    public const LOCATION_ANY = 'any';

    public const LOCATIONS = [
        self::LOCATION_HOME,
        self::LOCATION_GYM,
        self::LOCATION_OUTDOOR,
        self::LOCATION_ANY,
    ];

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'location_type',
        'time',
        'calories',
        'video_url',
        'is_premium',
    ];

    protected $hidden = [
        'video_url',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'calories' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userActivities()
    {
        return $this->hasMany(UserActivity::class);
    }

    public function scopeVisibleTo($query, User $user)
    {
        return $query->where(function ($inner) use ($user) {
            $inner->whereNull('user_id')->orWhere('user_id', $user->id);
        });
    }

    public function scopeForLocation($query, ?string $location)
    {
        if (!$location || $location === self::LOCATION_ANY) {
            return $query;
        }

        return $query->where(function ($inner) use ($location) {
            $inner->where('location_type', $location)
                ->orWhere('location_type', self::LOCATION_ANY);
        });
    }

    public function toApiArray(?User $user = null, bool $isFavorite = false): array
    {
        $hasVideo = filled($this->video_url);
        $canWatch = $hasVideo && $user && $user->isPremiumActive();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'time' => $this->time,
            'calories' => $this->calories,
            'category' => $this->category,
            'location_type' => $this->location_type ?? self::LOCATION_ANY,
            'is_custom' => $this->user_id !== null,
            'is_premium' => (bool) $this->is_premium,
            'has_video' => $hasVideo,
            'video_url' => $canWatch ? $this->video_url : null,
            'video_locked' => $hasVideo && !$canWatch,
            'is_favorite' => $isFavorite,
            'duration_minutes' => $this->durationMinutes(),
        ];
    }

    public function durationMinutes(): ?int
    {
        $time = trim((string) $this->time);
        if (!preg_match('/^(\d+)\s*(.*)$/u', $time, $matches)) {
            return null;
        }

        $count = (int) $matches[1];
        if ($count < 1) {
            return null;
        }

        $unit = mb_strtolower(trim($matches[2]));
        if ($unit === '' || str_starts_with($unit, 'мин') || str_starts_with($unit, 'min')) {
            return $count;
        }
        if (str_starts_with($unit, 'ч') || str_starts_with($unit, 'час') || str_starts_with($unit, 'hour')) {
            return $count * 60;
        }

        return null;
    }

    public function caloriesFor(int $timeCount, string $timeType): int
    {
        $baseMinutes = $this->durationMinutes();
        if (!$baseMinutes || $timeCount < 1 || !in_array($timeType, ['minute', 'hour'], true)) {
            throw new \InvalidArgumentException('Activity duration is not set');
        }

        $minutes = $timeType === 'hour' ? $timeCount * 60 : $timeCount;

        return (int) round($this->calories * $minutes / $baseMinutes);
    }
}
