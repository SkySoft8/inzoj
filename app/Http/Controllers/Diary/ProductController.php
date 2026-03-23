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
        $productId = $this->getData($request)[2];
        $product = Product::where('id', $productId)->first();
        $isFavorite = $request->get('is_favorite');
        $action = $request->get('action');

        if ($action == 'toggle_favorite') {
            if ($isFavorite == false) {
                UserFavoriteProduct::create([
                    'user_id' => $this->getData($request)[0],
                    'product_id' => $productId
                ]);
            } elseif ($isFavorite == true) {
                UserFavoriteProduct::where([
                    'user_id' => $this->getData($request)[0],
                    'product_id' => $productId
                ])->delete();
    
            }
            return redirect()->route('meal');
        }

        $amount = $this->getData($request)[4];
        if ($request->has('meal_type')) {
            session(['meal_type' => $request->get('meal_type')]);
        }

        return view('diary.product', [
            'product' => $product,
            'amount' => $amount
        ]);
    }

    public function addMealProduct (Request $request) {
        [$userId, $diaryNoteId, $productId, $mealType, $amount] = $this->getData($request);

        UserMeal::create([
            'user_id' => $userId,
            'diary_note_id' => $diaryNoteId,
            'product_id' => $productId,
            'meal_type' => $mealType,
            'amount' => $amount
        ]);

        return $this->recount($userId, $diaryNoteId);
    }

    public function updateMealProduct(Request $request) {
        $userMealId = $this->getData($request)[5];
        $amount = $request->get('amount');

        UserMeal::find($userMealId)->update(['amount' => $amount]);

        [$userId, $diaryNoteId] = $this->getData($request);

        return $this->recount($userId, $diaryNoteId);
    }

    private function recount($userId, $diaryNoteId) {
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

        return redirect()->route('diary');
    }

    private function getData(Request $request) {
        $userId = Auth::user()->id;
        $diaryNoteId = session('diary_note_id');
        $mealType = session('meal_type');

        $userMealId = session('user_meal_id');
        if ($userMealId) {
            $userMeal = UserMeal::find($userMealId);
            $productId = $userMeal->product_id;
            $amount = $userMeal->amount;
        } else {
            $productId = $request->get('product_id');
            $amount = $request->get('amount');
        }

        return [$userId, $diaryNoteId, $productId, $mealType, $amount, $userMealId];
    }
}
