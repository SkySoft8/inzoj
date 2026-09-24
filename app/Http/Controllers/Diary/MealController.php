<?php

namespace App\Http\Controllers\Diary;

use App\Models\Product;
use App\Models\UserFavoriteProduct;
use App\Models\UserFavoriteRecepie;
use App\Models\UserRecepie;

use App\Models\Recepies\Recepie;
use App\Models\Recepies\RecepieMealType;
use App\Models\Recepies\RecepieComponent;
use App\Models\Recepies\RecepieCookingMethod;
use App\Models\Recepies\RecepieDiet;

use App\Http\Controllers\Controller;
use App\Models\UserFavoriteUserRecepie;
use App\Services\DiaryBrowse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
{
    public function show (Request $request) {
        $user = Auth::user();

        $mealType = $request->get('meal_type');
        if (!$request->expectsJson() && isset($mealType)) {
            session(['meal_type' => $mealType]);
        }
        
        $productsOrRecepies = $request->get('productsOrRecepies') ?? 'products';

        if ($productsOrRecepies == 'products' && $request->expectsJson()) {
            $list = $request->get('list', 'frequent');
            if (!in_array($list, ['frequent', 'recent', 'favorites'], true)) {
                $list = 'frequent';
            }
            $products = app(DiaryBrowse::class)->products($user->id, $list, trim((string) $request->get('q', '')));

            return response()->json([
                'success' => true,
                'list' => $list,
                'products' => $products,
                'recepies' => null,
                'meal_type' => $mealType,
            ]);
        }

        if ($productsOrRecepies == 'recepies' && $request->expectsJson()) {
            $list = $request->get('list', 'all');
            if (!in_array($list, ['all', 'favorites'], true)) {
                $list = 'all';
            }
            $recepies = app(DiaryBrowse::class)->recepies($user->id, $list, trim((string) $request->get('q', '')));

            return response()->json([
                'success' => true,
                'list' => $list,
                'products' => null,
                'recepies' => $recepies,
                'meal_type' => $mealType,
            ]);
        }

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

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'products' => $products,
                    'recepies' => null,
                    'meal_type' => $mealType
                ]);
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
            $userRecepies = UserRecepie::with('items')->approved()->limit(16)->get();
            $userRecepies->each(function ($recepie) {
                $recepie->setAttribute('is_user_recepie', true);
            });

            $allRecepies = $userRecepies->concat($recepies);

            foreach ($allRecepies as $recepie) {
                $isCatalog = empty($recepie->is_user_recepie);
                $recepie->is_favorite = $isCatalog && in_array($recepie->id, $favoriteRecepiesId);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'products' => null,
                    'recepies' => $allRecepies,
                    'meal_type' => $mealType
                ]);
            }

            return view('diary.meal', ['products' => null, 'recepies' => $allRecepies]);            
        }
    }

    public function toggleFavorite(Request $request) {
        $userId = Auth::user()->id;
        $productsOrRecepies = $request->get('productsOrRecepies');
        $isFavorite = $request->get('is_favorite');

        if ($productsOrRecepies == 'products') {
            $productId = $request->get('product_id');
            if ($isFavorite == false) {
                UserFavoriteProduct::create([
                    'user_id' => $userId,
                    'product_id' => $productId
                ]);
            } elseif ($isFavorite == true) {
                UserFavoriteProduct::where([
                    'user_id' => $userId,
                    'product_id' => $productId
                ])->delete();    
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $isFavorite ? 'Product removed from favorites' : 'Product added to favorites',
                    'is_favorite' => !$isFavorite
                ]);
            }
            return redirect()->route('meal');

        } elseif ($productsOrRecepies == 'recepies') {
            $recepieId = $request->get('user_recepie_id') ?: $request->get('recepie_id');
            $isUserRecepie = $request->boolean('is_user_recepie') || $request->filled('user_recepie_id');
            $favorite = $isUserRecepie
                ? UserFavoriteUserRecepie::query()
                : UserFavoriteRecepie::query();
            $column = $isUserRecepie ? 'user_recepie_id' : 'recepie_id';
            if ($isFavorite == false) {
                $favorite->firstOrCreate([
                    'user_id' => $userId,
                    $column => $recepieId,
                ]);
            } elseif ($isFavorite == true) {
                $favorite->where([
                    'user_id' => $userId,
                    $column => $recepieId,
                ])->delete();
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $isFavorite ? 'Recepie removed from favorites' : 'Recepie added to favorites',
                    'is_favorite' => !$isFavorite
                ]);
            }
            return redirect()->route('meal');
        }
        return redirect()->route('meal');
    }

    public function showFilter(Request $request) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'filters' => app(DiaryBrowse::class)->filters(Auth::id())
            ]);
        }
        return view('diary.filters');
    }

    public function filterApply(Request $request) {
        $mealType = $request->get('meal_type', []);
        if (!is_array($mealType)) {
            $mealType = [$mealType];
        }
        $component = $request->get('component', []);
        if (!is_array($component)) {
            $component = [$component];
        }
        $cookingMethod = $request->get('cooking_method', []);
        if (!is_array($cookingMethod)) {
            $cookingMethod = [$cookingMethod];
        }
        $diet = $request->get('diet', []);
        if (!is_array($diet)) {
            $diet = [$diet];
        }

        $recepies = app(DiaryBrowse::class)->applyFilters(Auth::id(), [
            'meal_type' => array_values(array_filter($mealType)),
            'component' => array_values(array_filter($component)),
            'cooking_method' => array_values(array_filter($cookingMethod)),
            'diet' => array_values(array_filter($diet)),
        ]);
        
        if (count($recepies) == 0) {
            $recepies = null;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'products' => null,
                'recepies' => $recepies,
                'meal_type' => $mealType,
                'component' => $component,
                'cooking_method' => $cookingMethod,
                'diet' => $diet
            ]);
        }

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
