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

    protected $appends = [
        'image_url',
        'steps',
        'is_user_recepie',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return url($this->image);
    }

    public function getStepsAttribute(): array
    {
        $text = trim((string) ($this->attributes['instructions'] ?? ''));

        return $text === '' ? [] : [$text];
    }

    public function getIsUserRecepieAttribute(): bool
    {
        return false;
    }

}
