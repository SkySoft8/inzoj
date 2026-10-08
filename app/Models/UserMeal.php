<?php

namespace App\Models;

use App\Models\Recepies\Recepie;
use App\Models\Restaurants\Dish;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'diary_note_id',
        'product_id',
        'recepie_id',
        'user_recepie_id',
        'dish_id',
        'meal_type',
        'amount',
        'portion_quantity',
        'portion_unit',
    ];

    public function portionLabel(): string
    {
        $quantity = $this->portion_quantity ?? $this->amount;
        $unit = $this->portion_unit ?? 'g';
        $label = ['g' => 'г', 'pcs' => 'шт', 'ml' => 'мл'][$unit] ?? 'г';

        return $quantity.' '.$label;
    }

    public function nutritionSource(): ?array
    {
        if ($this->user_recepie_id) {
            $item = UserRecepie::find($this->user_recepie_id);

            return $item ? ['type' => 'user_recepie', 'item' => $item] : null;
        }

        if ($this->recepie_id) {
            $item = Recepie::find($this->recepie_id);

            return $item ? ['type' => 'recepie', 'item' => $item] : null;
        }

        if ($this->product_id) {
            $item = Product::find($this->product_id);

            return $item ? ['type' => 'product', 'item' => $item] : null;
        }

        if ($this->dish_id) {
            $item = Dish::find($this->dish_id);

            return $item ? ['type' => 'dish', 'item' => $item] : null;
        }

        return null;
    }
}
