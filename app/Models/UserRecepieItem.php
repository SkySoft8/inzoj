<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRecepieItem extends Model
{
    protected $fillable = [
        'user_recepie_id',
        'product_id',
        'grams',
    ];

    protected $casts = [
        'grams' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function userRecepie()
    {
        return $this->belongsTo(UserRecepie::class);
    }
}
