<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFilterView extends Model
{
    protected $fillable = [
        'user_id',
        'filter_group',
        'filter_value',
        'hits',
    ];
}
