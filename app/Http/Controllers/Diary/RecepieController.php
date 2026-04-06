<?php

namespace App\Http\Controllers\Diary;

use App\Models\Recepies\Recepie;
use App\Models\Recepies\RecepieIngredient;
use App\Models\UserFavoriteRecepie;

use App\Models\Product;
use App\Models\Restaurants\Dish; 
use App\Models\UserMeal;
use App\Models\DiaryNote;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecepieController extends Controller
{
    public function show (Request $request) {
        [$userId, $diaryNoteId, $recepieId, $mealType, $amount, $userMealId] = $this->getData($request);        $previousUrl = url()->previous();

        $recepie = Recepie::where('id', $recepieId)->first();
        if (!$recepie && $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Recipe not found'
            ], 404);
        }

        $ingredients = RecepieIngredient::where('recepie_id', $recepieId)->get();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'recepie' => $recepie,
                'ingredients' => $ingredients,
                'amount' => $amount,
                'diary_note_id' => $diaryNoteId,
                'user_meal_id' => $userMealId
            ]);
        }

        $previousUrl = url()->previous();
        return view('diary.recepie', [
            'recepie' => $recepie,
            'ingredients' => $ingredients,
            'amount' => $amount,
            'url' => $previousUrl,
        ]);

    }

    public function addMealRecepie (Request $request) {
        [$userId, $diaryNoteId, $recepieId, $mealType, $amount] = $this->getData($request);

        $userMeal = UserMeal::create([
            'user_id' => $userId,
            'diary_note_id' => $diaryNoteId,
            'recepie_id' => $recepieId,
            'meal_type' => $mealType,
            'amount' => $amount
        ]);

        $userMealId = $userMeal->id;

        return $this->recount($userId, $diaryNoteId, true, $request, $userMealId);
    }

    public function updateMealRecepie(Request $request) {
        $userMealId = $this->getData($request)[5];
        $amount = $request->get('amount');

        $user_meal = UserMeal::find($userMealId)->update(['amount' => $amount]);

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

        $diaryNote = DiaryNote::find($diaryNoteId);

        if (!$diaryNote) {
            if ($request && $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diary note not found'
                ], 404);
            }
            return redirect()->route('diary')->with('error', 'Diary note not found');
        }    

        $diaryNote->update([
            'current_calories' => $newData['calories'],
            'current_proteins' => $newData['proteins'],
            'current_fats' => $newData['fats'],
            'current_carbs' => $newData['carbs']
        ]);

        $diaryNote->update([
            'current_calories' => $newData['calories'],
            'current_proteins' => $newData['proteins'],
            'current_fats' => $newData['fats'],
            'current_carbs' => $newData['carbs']
        ]);

        if ($request && $request->expectsJson()) {           
            return response()->json([
                'success' => true,
                'message' => $adding ? 'Recipe added to meal successfully' : 'Recipe amount updated successfully',
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
            $recepieId = $userMeal->recepie_id;
            $mealType = $userMeal->meal_type;
            $amount = $userMeal->amount;
        } else {
            $recepieId = $request->get('recepie_id');
            $mealType = $request->get('meal_type') ?? session('meal_type');
            $amount = $request->get('amount');
        }

        return [$userId, $diaryNoteId, $recepieId, $mealType, $amount, $userMealId];
    }
}
