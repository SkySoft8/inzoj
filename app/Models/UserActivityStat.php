<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityStat extends Model
{
    protected $fillable = [
        'user_id',
        'activity_id',
        'times',
        'last_added_at',
    ];
}
