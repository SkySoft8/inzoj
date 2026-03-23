<?php

namespace App\Http\Controllers\Diary;

use App\Models\Product;
use App\Models\UserFavoriteProduct;
use App\Models\UserFavoriteRecepie;

use App\Models\Recepies\Recepie;
use App\Models\Recepies\RecepieMealType;
use App\Models\Recepies\RecepieComponent;
use App\Models\Recepies\RecepieCookingMethod;
use App\Models\Recepies\RecepieDiet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
{
    public function show (Request $request) {
        $user = Auth::user();

        $mealType = $request->get('meal_type');
        if (isset($mealType)) {
            session()->put('meal_type', $mealType);
        }

        $productsOrRecepies = $request->get('productsOrRecepies') ?? 'products';

        if ($productsOrRecepies == 'products') {
            $favoriteProductsId = UserFavoriteProduct::where('user_id', $user->id)
                ->pluck('product_id')
                ->toArray();
            if (!empty($favoriteProductsId)) {
                $favorites = Product::whereIn('id', $favoriteProductsId)->limit(16)->get();
                $others = Product::whereNotIn('id', $favoriteProductsId)->limit(16 - $favorites->count())->get();
                $products = $favorites->concat($others);
            } else {
                $products = Product::limit(16)->get();
            }
            
            foreach ($products as $product) {
                if (in_array($product->id, $favoriteProductsId)) {
                    $product->is_favorite = true;
                } else {
                    $product->is_favorite = false;
                }
            }

            return view('diary.meal', ['products' => $products, 'recepies' => null]);
        } elseif ($productsOrRecepies == 'recepies') {
            $favoriteRecepiesId = UserFavoriteRecepie::where('user_id', $user->id)
                ->pluck('recepie_id')
                ->toArray();
            if (!empty($favoriteRecepiesId)) {
                $favorites = Recepie::whereIn('id', $favoriteRecepiesId)->limit(16)->get();
                $others = Recepie::whereNotIn('id', $favoriteRecepiesId)->limit(16 - $favorites->count())->get();
                $recepies = $favorites->concat($others);
            } else {
                $recepies = Recepie::limit(16)->get();
            }

            foreach ($recepies as $recepie) {
                if (in_array($recepie->id, $favoriteRecepiesId)) {
                    $recepie->is_favorite = true;
                } else {
                    $recepie->is_favorite = false;
                }
            }

            return view('diary.meal', ['products' => null, 'recepies' => $recepies]);            
        }
    }

    public function filterApply(Request $request) {
        $mealType = $request->get('meal_type') ?? null;
        $component = $request->get('component') ?? null;
        $cookingMethod = $request->get('cooking_method') ?? null;
        $diet = $request->get('diet') ?? null;

        $allFilters = [
            RecepieMealType::class => [$mealType, 'meal_type'],
            RecepieComponent::class => [$component, 'component'],
            RecepieCookingMethod::class => [$cookingMethod, 'cooking_method'],
            RecepieDiet::class => [$diet, 'diet']
        ];

        $correctIds = [];
        foreach ($allFilters as $modelClass => [$filterType, $fieldName]) {
            if ($filterType) {
                foreach ($filterType as $filter) {
                    $newIds = $modelClass::where($fieldName, $filter)
                        ->pluck('recepie_id')
                        ->toArray();

                    $correctIds = array_merge($correctIds, $newIds);
                }
            }
        }
        
        $uniqueIds = array_unique($correctIds);
        $recepies = [];

        $recepies = Recepie::whereIn('id', $uniqueIds)
            ->limit(6)
            ->get();
        
        if (count($recepies) == 0) {
            $recepies = null;
        }        
        // $recepies = Recepie::limit(6)->get();
        return view('diary.meal', [
            'products' => null,
            'recepies' => $recepies,
            'mealType' => $mealType,
            'component' => $component,
            'cookingMethod' => $cookingMethod,
            'diet' => $diet
        ]);
    }
}
