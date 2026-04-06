<?php

namespace App\Http\Controllers\Diary;

use App\Models\DiaryNote;
use App\Models\UserMeal;
use App\Models\Product;
use App\Models\Recepies\Recepie;
use App\Models\Restaurants\Dish; 

use App\Models\UserFavoriteProduct;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function show (Request $request) {
        [$userId, $diaryNoteId, $productId, $mealType, $amount, $userMealId] = $this->getData($request);

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
        [$userId, $diaryNoteId, $productId, $mealType, $amount] = $this->getData($request);

        $userMeal = UserMeal::create([
            'user_id' => $userId,
            'diary_note_id' => $diaryNoteId,
            'product_id' => $productId,
            'meal_type' => $mealType,
            'amount' => $amount
        ]);

        $userMealId = $userMeal->id;

        return $this->recount($userId, $diaryNoteId, true, $request, $userMealId);
    }

    public function updateMealProduct(Request $request) {
        $userMealId = $this->getData($request)[5];
        $amount = $request->get('amount');

        UserMeal::find($userMealId)->update(['amount' => $amount]);

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
            if ($meal->recepie_id) {
                foreach (array_keys($newData) as $key) {
                    $recepie = Recepie::find($meal->recepie_id);
                    $newData[$key] += round($recepie->$key * $meal->amount / 100, 1);
                }
            } elseif ($meal->product_id) {
                foreach (array_keys($newData) as $key) {
                    $product = Product::find($meal->product_id);
                    $newData[$key] += round($product->$key * $meal->amount / 100, 1);
                }
            } elseif ($meal->dish_id) {
                foreach (array_keys($newData) as $key) {
                    $dish = Dish::find($meal->dish_id);
                    $newData[$key] += round($dish->$key * $meal->amount / 100, 1);
                }
            }
        }

        DiaryNote::find($diaryNoteId)->update([
            'current_calories' => $newData['calories'],
            'current_proteins' => $newData['proteins'],
            'current_fats' => $newData['fats'],
            'current_carbs' => $newData['carbs']
        ]);

        if ($request->expectsJson()) {           
            return response()->json([
                'success' => true,
                'message' => $adding ? 'Product added to meal successfully' : 'Product amount updated successfully',
                'user_meal_id' => $userMealId,
                'diary_note_id' => $diaryNoteId
            ]);
        }

        return redirect()->route('diary');
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
