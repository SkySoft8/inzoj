<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Recepies\Recepie;
use App\Models\Recepies\RecepieComponent;
use App\Models\Recepies\RecepieCookingMethod;
use App\Models\Recepies\RecepieDiet;
use App\Models\Recepies\RecepieMealType;
use App\Models\UserFavoriteProduct;
use App\Models\UserFavoriteRecepie;
use App\Models\UserFavoriteUserRecepie;
use App\Models\UserFilterView;
use App\Models\UserProductStat;
use App\Models\UserRecepie;
use Illuminate\Support\Collection;

class DiaryBrowse
{
    public const FILTERS = [
        'meal_type' => ['breakfast', 'lunch', 'dinner', 'snack'],
        'component' => ['poultry', 'meat', 'fish', 'vegetables', 'fruits', 'sweet'],
        'cooking_method' => ['boiled', 'steamed', 'fried', 'stew', 'baked', 'basic'],
        'diet' => ['vegetarian', 'vegan', 'low_fat', 'lots_of_fiber', 'low_carb', 'keto_diet', 'high_protein', 'lactose_free'],
    ];

    public const LABELS = [
        'breakfast' => 'Завтрак',
        'lunch' => 'Обед',
        'dinner' => 'Ужин',
        'snack' => 'Перекус',
        'poultry' => 'Птица',
        'meat' => 'Мясо',
        'fish' => 'Рыба',
        'vegetables' => 'Овощи',
        'fruits' => 'Фрукты',
        'sweet' => 'Сладкое',
        'boiled' => 'Вареное',
        'steamed' => 'На пару',
        'fried' => 'Жареное',
        'stew' => 'Тушеное',
        'baked' => 'Запеченое',
        'basic' => 'Базовое',
        'vegetarian' => 'Вегетарианский',
        'vegan' => 'Веганский',
        'low_fat' => 'Мало жиров',
        'lots_of_fiber' => 'Много клетчатки',
        'low_carb' => 'Низкоуглеводный',
        'keto_diet' => 'Кетодиета',
        'high_protein' => 'Высокобелковый',
        'lactose_free' => 'Без лактозы',
    ];

    private const DEFAULT_POPULAR = [
        ['group' => 'meal_type', 'value' => 'breakfast'],
        ['group' => 'meal_type', 'value' => 'lunch'],
        ['group' => 'meal_type', 'value' => 'dinner'],
        ['group' => 'diet', 'value' => 'low_carb'],
        ['group' => 'diet', 'value' => 'high_protein'],
    ];

    public function products(int $userId, string $list, string $query): Collection
    {
        $favoriteIds = UserFavoriteProduct::where('user_id', $userId)->pluck('product_id');

        $products = match ($list) {
            'favorites' => Product::whereIn('id', $favoriteIds)->orderBy('name')->limit(30)->get(),
            'recent' => $this->recentProducts($userId),
            default => $this->frequentProducts($userId),
        };

        if ($query !== '') {
            $needle = mb_strtolower($query);
            $products = $products->filter(
                fn (Product $product) => str_contains(mb_strtolower($product->name), $needle)
            )->values();
        }

        return $products->each(function (Product $product) use ($favoriteIds) {
            $product->is_favorite = $favoriteIds->contains($product->id);
        });
    }

    public function recepies(int $userId, string $list, string $query): Collection
    {
        $catalogFavoriteIds = UserFavoriteRecepie::where('user_id', $userId)->pluck('recepie_id');
        $userFavoriteIds = UserFavoriteUserRecepie::where('user_id', $userId)->pluck('user_recepie_id');

        $userRecepies = UserRecepie::with('items')->approved()
            ->when($list === 'favorites', fn ($builder) => $builder->whereIn('id', $userFavoriteIds))
            ->when($query !== '', fn ($builder) => $builder->where('name', 'like', '%'.$query.'%'))
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        $catalog = Recepie::query()
            ->when($list === 'favorites', fn ($builder) => $builder->whereIn('id', $catalogFavoriteIds))
            ->when($query !== '', fn ($builder) => $builder->where('name', 'like', '%'.$query.'%'))
            ->orderBy('name')
            ->limit(30)
            ->get();

        return $userRecepies->concat($catalog)->each(function ($recepie) use ($catalogFavoriteIds, $userFavoriteIds) {
            $isUser = $recepie instanceof UserRecepie;
            $recepie->is_favorite = $isUser
                ? $userFavoriteIds->contains($recepie->id)
                : $catalogFavoriteIds->contains($recepie->id);
            $recepie->bju = $isUser
                ? $recepie->per_portion
                : [
                    'grams' => null,
                    'calories' => (float) $recepie->calories,
                    'proteins' => (float) $recepie->proteins,
                    'fats' => (float) $recepie->fats,
                    'carbs' => (float) $recepie->carbs,
                ];
        })->values();
    }

    public function applyFilters(int $userId, array $selected): Collection
    {
        $this->remember($userId, $selected);

        $catalogIds = null;
        $map = [
            'meal_type' => [RecepieMealType::class, 'meal_type'],
            'component' => [RecepieComponent::class, 'component'],
            'cooking_method' => [RecepieCookingMethod::class, 'cooking_method'],
            'diet' => [RecepieDiet::class, 'diet'],
        ];

        foreach ($map as $group => [$model, $field]) {
            $values = $selected[$group] ?? [];
            if ($values === []) {
                continue;
            }
            $ids = $model::whereIn($field, $values)->pluck('recepie_id')->unique();
            $catalogIds = $catalogIds === null ? $ids : $catalogIds->intersect($ids);
        }

        $catalog = $catalogIds === null
            ? Recepie::orderBy('name')->limit(30)->get()
            : Recepie::whereIn('id', $catalogIds)->orderBy('name')->limit(30)->get();

        $userQuery = UserRecepie::with('items')->approved();
        foreach ($selected as $group => $values) {
            if ($values === []) {
                continue;
            }
            $column = $group === 'meal_type' ? 'meal_types'
                : ($group === 'component' ? 'components'
                : ($group === 'cooking_method' ? 'cooking_methods' : 'diets'));
            $userQuery->where(function ($builder) use ($column, $values) {
                foreach ($values as $value) {
                    $builder->orWhereJsonContains($column, $value);
                }
            });
        }

        $userRecepies = $userQuery->orderByDesc('id')->limit(30)->get();
        $catalogFavoriteIds = UserFavoriteRecepie::where('user_id', $userId)->pluck('recepie_id');
        $userFavoriteIds = UserFavoriteUserRecepie::where('user_id', $userId)->pluck('user_recepie_id');

        return $userRecepies->concat($catalog)->each(function ($recepie) use ($catalogFavoriteIds, $userFavoriteIds) {
            $isUser = $recepie instanceof UserRecepie;
            $recepie->is_favorite = $isUser
                ? $userFavoriteIds->contains($recepie->id)
                : $catalogFavoriteIds->contains($recepie->id);
        })->values();
    }

    public function filters(int $userId): array
    {
        return [
            'popular' => $this->popular($userId),
            'meal_types' => $this->options('meal_type'),
            'components' => $this->options('component'),
            'cooking_methods' => $this->options('cooking_method'),
            'diets' => $this->options('diet'),
        ];
    }

    public function rememberRecipe(int $userId, array $tags): void
    {
        $this->remember($userId, $tags);
    }

    public function remember(int $userId, array $selected): void
    {
        foreach ($selected as $group => $values) {
            if (!isset(self::FILTERS[$group])) {
                continue;
            }
            foreach ($values as $value) {
                if (!in_array($value, self::FILTERS[$group], true)) {
                    continue;
                }
                $row = UserFilterView::firstOrCreate(
                    ['user_id' => $userId, 'filter_group' => $group, 'filter_value' => $value],
                    ['hits' => 0]
                );
                $row->increment('hits');
            }
        }
    }

    private function popular(int $userId): array
    {
        $rows = UserFilterView::where('user_id', $userId)->orderByDesc('hits')->limit(5)->get();
        $items = $rows->map(fn (UserFilterView $row) => [
            'group' => $row->filter_group,
            'value' => $row->filter_value,
            'label' => self::LABELS[$row->filter_value] ?? $row->filter_value,
        ])->all();

        foreach (self::DEFAULT_POPULAR as $item) {
            if (count($items) >= 5) {
                break;
            }
            $exists = collect($items)->contains(fn ($row) => $row['group'] === $item['group'] && $row['value'] === $item['value']);
            if (!$exists) {
                $items[] = $item + ['label' => self::LABELS[$item['value']]];
            }
        }

        return $items;
    }

    private function options(string $group): array
    {
        return array_map(fn (string $value) => [
            'value' => $value,
            'label' => self::LABELS[$value] ?? $value,
        ], self::FILTERS[$group]);
    }

    public function rememberProduct(int $userId, int $productId): void
    {
        $stat = UserProductStat::firstOrCreate(
            ['user_id' => $userId, 'product_id' => $productId],
            ['times' => 0]
        );
        $stat->increment('times');
        $stat->update(['last_added_at' => now()]);
    }

    private function frequentProducts(int $userId): Collection
    {
        $ids = UserProductStat::where('user_id', $userId)
            ->orderByDesc('times')
            ->limit(30)
            ->pluck('product_id');

        if ($ids->isEmpty()) {
            return Product::orderBy('name')->limit(30)->get();
        }

        return Product::whereIn('id', $ids)->get()->sortBy(fn (Product $product) => $ids->search($product->id))->values();
    }

    private function recentProducts(int $userId): Collection
    {
        $ids = UserProductStat::where('user_id', $userId)
            ->orderByDesc('last_added_at')
            ->limit(30)
            ->pluck('product_id');

        if ($ids->isEmpty()) {
            return collect();
        }

        return Product::whereIn('id', $ids)->get()->sortBy(fn (Product $product) => $ids->search($product->id))->values();
    }
}
