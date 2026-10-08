<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'diary_note_id',
        'activity_id',
        'training_id',
        'time_count',
        'time_type',
        'calories'
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function diaryName(): ?string
    {
        if ($this->training) {
            $minutes = (int) $this->training->time_amount;

            return $minutes > 0
                ? $this->training->name.' ('.$minutes.' мин)'
                : $this->training->name;
        }

        if ($this->activity) {
            return $this->activity->name;
        }

        if ($this->activity_id === null && (int) $this->time_count > 0) {
            $unit = $this->time_type === 'hour' ? 'ч' : 'мин';

            return 'Тренировка ('.$this->time_count.' '.$unit.')';
        }

        return null;
    }
}
