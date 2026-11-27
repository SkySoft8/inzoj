<?php

namespace App\Services;

use App\Models\Product;
use App\Models\UserMeal;
use App\Models\UserRecepie;

class NutrientIntake
{
    /**
     * Sums micronutrients for the given diary notes.
     *
     * Products and user recipes made from products carry nutrient values.
     * Catalogue recipes and restaurant dishes do not expose them yet, so the
     * result also reports how much of the eaten weight could be evaluated.
     */
    public static function forNotes(int $userId, array $noteIds, array $codes): array
    {
        $totals = array_fill_keys($codes, 0.0);
        $totalGrams = 0.0;
        $knownGrams = 0.0;

        if ($noteIds === [] || $codes === []) {
            return [
                'totals' => $totals,
                'total_grams' => $totalGrams,
                'known_grams' => $knownGrams,
            ];
        }

        $meals = UserMeal::where('user_id', $userId)
            ->whereIn('diary_note_id', $noteIds)
            ->get();

        $products = Product::whereIn('id', $meals->pluck('product_id')->filter()->unique()->all())
            ->get()
            ->keyBy('id');

        $recipes = UserRecepie::with('items.product')
            ->whereIn('id', $meals->pluck('user_recepie_id')->filter()->unique()->all())
            ->get()
            ->keyBy('id');

        foreach ($meals as $meal) {
            $grams = (float) $meal->amount;
            if ($grams <= 0) {
                continue;
            }

            $totalGrams += $grams;

            if ($meal->product_id) {
                $product = $products->get($meal->product_id);
                if (!self::hasNutrients($product)) {
                    continue;
                }

                self::addPortion($totals, $product->nutrients, $grams, $codes);
                $knownGrams += $grams;

                continue;
            }

            if ($meal->user_recepie_id) {
                $recipe = $recipes->get($meal->user_recepie_id);
                if (!$recipe || $recipe->items->isEmpty()) {
                    continue;
                }

                $recipeGrams = (float) $recipe->items->sum('grams');
                if ($recipeGrams <= 0) {
                    continue;
                }

                $per100 = array_fill_keys($codes, 0.0);
                $knownRecipeGrams = 0.0;

                foreach ($recipe->items as $item) {
                    if (!self::hasNutrients($item->product)) {
                        continue;
                    }

                    $ratio = (float) $item->grams / 100;
                    foreach ($codes as $code) {
                        $per100[$code] += (float) ($item->product->nutrients[$code] ?? 0) * $ratio;
                    }
                    $knownRecipeGrams += (float) $item->grams;
                }

                if ($knownRecipeGrams <= 0) {
                    continue;
                }

                $factor = $recipeGrams / 100;
                foreach ($codes as $code) {
                    $totals[$code] += ($per100[$code] / $factor) * ($grams / 100);
                }
                $knownGrams += $grams * ($knownRecipeGrams / $recipeGrams);
            }
        }

        return [
            'totals' => $totals,
            'total_grams' => $totalGrams,
            'known_grams' => $knownGrams,
        ];
    }

    private static function hasNutrients(?Product $product): bool
    {
        return $product && is_array($product->nutrients) && $product->nutrients !== [];
    }

    private static function addPortion(array &$totals, array $nutrients, float $grams, array $codes): void
    {
        $ratio = $grams / 100;
        foreach ($codes as $code) {
            $totals[$code] += (float) ($nutrients[$code] ?? 0) * $ratio;
        }
    }
}
