<?php

namespace App\Http\Controllers\Diary;

use App\Models\DiaryNote;
use App\Models\UserMeal;
use App\Models\Product;

use App\Models\UserFavoriteProduct;


use App\Http\Controllers\Controller;
use App\Services\DiaryBrowse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function search(Request $request)
    {
        $query = trim((string) $request->get('q', ''));

        $products = Product::query()
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where('name', 'like', '%'.$query.'%');
            })
            ->orderBy('name')
            ->limit(30)
            ->get(['id', 'name', 'calories', 'proteins', 'fats', 'carbs', 'serving_label', 'serving_grams', 'serving_unit']);

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    public function show (Request $request) {
        [$userId, $diaryNoteId, $productId, $mealType, $amount, $userMealId] = $this->getData($request);

        $meal = null;
        if ($userMealId) {
            $meal = UserMeal::where('id', $userMealId)->where('user_id', $userId)->first();
            if (!$meal) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User meal not found',
                    ], 404);
                }
                abort(404, 'User meal not found');
            }
            $productId = $meal->product_id;
            $amount = $meal->amount;
        }

        $product = Product::where('id', $productId)->first();

        if (!$product && $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'product' => $product,
                'amount' => $amount,
                'portion_quantity' => $meal?->portion_quantity,
                'portion_unit' => $meal?->portion_unit,
                'portion_label' => $meal?->portionLabel(),
                'diary_note_id' => $diaryNoteId,
                'user_meal_id' => $userMealId
            ]);
        }

        return view('diary.product', [
            'product' => $product,
            'amount' => $amount
        ]);
    }

    public function addMealProduct (Request $request) {
        [$userId, $diaryNoteId, $productId, $mealType] = $this->getData($request);
        $portion = $this->portionFromRequest($request, $productId);
        if ($portion instanceof \Illuminate\Http\JsonResponse) {
            return $portion;
        }

        $userMeal = UserMeal::create([
            'user_id' => $userId,
            'diary_note_id' => $diaryNoteId,
            'product_id' => $productId,
            'meal_type' => $mealType,
            'amount' => $portion['grams'],
            'portion_quantity' => $portion['quantity'],
            'portion_unit' => $portion['unit'],
        ]);

        app(DiaryBrowse::class)->rememberProduct($userId, (int) $productId);

        $userMealId = $userMeal->id;

        return $this->recount($userId, $diaryNoteId, true, $request, $userMealId);
    }

    public function updateMealProduct(Request $request) {
        $userMealId = $this->getData($request)[5];
        $meal = UserMeal::find($userMealId);
        $portion = $this->portionFromRequest($request, $meal->product_id);
        if ($portion instanceof \Illuminate\Http\JsonResponse) {
            return $portion;
        }

        $meal->update([
            'amount' => $portion['grams'],
            'portion_quantity' => $portion['quantity'],
            'portion_unit' => $portion['unit'],
        ]);

        [$userId, $diaryNoteId] = $this->getData($request);

        return $this->recount($userId, $diaryNoteId, false, $request, $userMealId);
    }

    private function recount($userId, $diaryNoteId, $adding, $request, $userMealId) {
        $currentMeals = UserMeal::where('user_id', $userId)
            ->where('diary_note_id', $diaryNoteId)
            ->get();

        $newData = [
            'calories' => 0,
            'proteins' => 0,
            'fats' => 0,
            'carbs' => 0
        ];

        foreach ($currentMeals as $meal) {
            $source = $meal->nutritionSource();
            if (!$source) {
                continue;
            }
            $ratio = $meal->amount / 100;
            foreach (array_keys($newData) as $key) {
                $newData[$key] += round($source['item']->{$key} * $ratio, 1);
            }
        }

        DiaryNote::find($diaryNoteId)->update([
            'current_calories' => $newData['calories'],
            'current_proteins' => $newData['proteins'],
            'current_fats' => $newData['fats'],
            'current_carbs' => $newData['carbs']
        ]);

        if ($request->expectsJson()) {
            $saved = UserMeal::find($userMealId);

            return response()->json([
                'success' => true,
                'message' => $adding ? 'Product added to meal successfully' : 'Product amount updated successfully',
                'user_meal_id' => $userMealId,
                'diary_note_id' => $diaryNoteId,
                'amount' => $saved->amount,
                'portion_quantity' => $saved->portion_quantity,
                'portion_unit' => $saved->portion_unit,
                'portion_label' => $saved->portionLabel(),
            ]);
        }

        return redirect()->route('diary');
    }

    private function portionFromRequest(Request $request, $productId)
    {
        $unit = $request->get('unit', 'g');
        $quantity = $request->get('quantity', $request->get('amount'));

        if (!in_array($unit, ['g', 'pcs', 'ml'], true) || !is_numeric($quantity) || (int) $quantity < 1) {
            return response()->json([
                'success' => false,
                'message' => 'quantity and unit are required',
            ], 422);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        try {
            $grams = $product->gramsForPortion((int) $quantity, $unit);
        } catch (\InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return [
            'quantity' => (int) $quantity,
            'unit' => $unit,
            'grams' => $grams,
        ];
    }

    private function getData(Request $request) {
        $userId = Auth::user()->id;

        $diaryNoteId = $request->get('diary_note_id') ?? session('diary_note_id');
        $userMealId = $request->get('user_meal_id') ?? session('user_meal_id');

        if ($userMealId) {
            $userMeal = UserMeal::find($userMealId);
            $productId = $userMeal->product_id;
            $mealType = $userMeal->meal_type;
            $amount = $userMeal->amount;
        } else {
            $productId = $request->get('product_id');
            $mealType = $request->get('meal_type') ?? session('meal_type');
            $amount = $request->get('amount');
        }

        return [$userId, $diaryNoteId, $productId, $mealType, $amount, $userMealId];
    }
}
