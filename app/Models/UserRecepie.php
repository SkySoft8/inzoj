<?php

namespace App\Models;

use App\Models\Concerns\HasModeration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRecepie extends Model
{
    use HasFactory, HasModeration;

    protected $fillable = [
        'user_id',
        'name',
        'calories',
        'proteins',
        'fats',
        'carbs',
        'portions',
        'image',
        'steps',
        'meal_types',
        'components',
        'cooking_methods',
        'diets',
        'moderation_status',
        'moderated_at',
        'moderation_comment',
    ];

    protected $casts = [
        'moderated_at' => 'datetime',
        'portions' => 'integer',
        'meal_types' => 'array',
        'components' => 'array',
        'cooking_methods' => 'array',
        'diets' => 'array',
        'calories' => 'float',
        'proteins' => 'float',
        'fats' => 'float',
        'carbs' => 'float',
    ];

    protected $appends = [
        'image_url',
        'per_portion',
        'is_user_recepie',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStepsAttribute($value): array
    {
        $steps = is_array($value) ? $value : json_decode($value ?? '', true);

        return is_array($steps) ? array_values($steps) : [];
    }

    public function setStepsAttribute($value): void
    {
        if ($value === null || $value === []) {
            $this->attributes['steps'] = null;

            return;
        }

        $this->attributes['steps'] = is_string($value)
            ? $value
            : json_encode(array_values($value), JSON_UNESCAPED_UNICODE);
    }

    public function ingredients()
    {
        return $this->hasMany(UserRecepieIngridient::class);
    }

    public function items()
    {
        return $this->hasMany(UserRecepieItem::class);
    }

    public function getIsUserRecepieAttribute(): bool
    {
        return true;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return url('storage/'.$this->image);
    }

    public function totalGrams(): int
    {
        $grams = $this->relationLoaded('items')
            ? $this->items->sum('grams')
            : (int) $this->items()->sum('grams');

        return (int) $grams;
    }

    public function portionGrams(): int
    {
        $total = $this->totalGrams();
        if ($total <= 0) {
            return 100;
        }

        return max(1, (int) round($total / max(1, (int) $this->portions)));
    }

    public function getPerPortionAttribute(): array
    {
        $grams = $this->portionGrams();
        $ratio = $grams / 100;

        return [
            'grams' => $grams,
            'calories' => round($this->calories * $ratio, 1),
            'proteins' => round($this->proteins * $ratio, 1),
            'fats' => round($this->fats * $ratio, 1),
            'carbs' => round($this->carbs * $ratio, 1),
        ];
    }

    public static function nutritionFromProducts(array $ingredients, int $portions): array
    {
        $ids = collect($ingredients)->pluck('product_id')->unique()->all();
        $products = Product::whereIn('id', $ids)->get()->keyBy('id');

        $totals = ['calories' => 0, 'proteins' => 0, 'fats' => 0, 'carbs' => 0];
        $grams = 0;
        $lines = [];

        foreach ($ingredients as $row) {
            $product = $products->get($row['product_id']);
            $lineGrams = (int) $row['grams'];
            $grams += $lineGrams;
            $ratio = $lineGrams / 100;

            foreach (array_keys($totals) as $key) {
                $totals[$key] += (float) $product->{$key} * $ratio;
            }

            $lines[] = [
                'product_id' => $product->id,
                'grams' => $lineGrams,
            ];
        }

        $per100 = $grams > 0 ? $grams / 100 : 1;

        return [
            'calories' => round($totals['calories'] / $per100, 1),
            'proteins' => round($totals['proteins'] / $per100, 1),
            'fats' => round($totals['fats'] / $per100, 1),
            'carbs' => round($totals['carbs'] / $per100, 1),
            'lines' => $lines,
            'total_grams' => $grams,
        ];
    }
}
