<?php

namespace App\Http\Controllers\Restaurant;

use App\Models\DiaryNote;
use App\Models\UserMeal;
use App\Models\Product;
use App\Models\Recepies\Recepie;
use App\Models\Restaurants\Dish; 


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DishController extends Controller
{
    public function show(Request $request) {
        [$userId, $diaryNoteId, $dishId, $proteins, $fats, $carbs, $calories, $userMealId] = $this->getData($request);

        $dish = Dish::find($dishId);
        
        if (!$dish && $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Dish not found'
            ], 404);
        }

        $nutrients = [
            $proteins, 
            $fats,
            $carbs,
            $calories
        ];

        $adding = $userMealId !== null ? false : true;

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'dish' => $dish,
                'nutrients' => $nutrients,
                'adding' => $adding,
                'diary_note_id' => $diaryNoteId,
                'user_meal_id' => $userMealId
            ]);
        }

        return view('restaurant.dish', [
            'dish' => $dish,
            'nutrients' => $nutrients,
            'adding' => $adding,
        ]);
    }

    public function addToDiary(Request $request) {
        [$userId, $diaryNoteId, $dishId, $proteins, $fats, $carbs, $calories, $userMealId] = $this->getData($request);

        $dish = Dish::find($dishId);

        if (!$dish && $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Dish not found'
            ], 404);
        }        

        $dishWeight = $dish->weight;
        $mealType = $request->get('meal_type');

        $user_meal = UserMeal::create([
            'user_id' => $userId,
            'diary_note_id' => $diaryNoteId,
            'dish_id' => $dishId,
            'meal_type' => $mealType,
            'amount' => $dishWeight
        ]);

        $newUserMealId = $user_meal->id;

        return $this->recount($userId, $diaryNoteId, $request, $newUserMealId);
    }

    public function deleteFromDiary(Request $request) {
        [$userId, $diaryNoteId, $dishId, $proteins, $fats, $carbs, $calories, $userMealId] = $this->getData($request);        

        $userMeal = UserMeal::find($userMealId);

        if (!$userMeal && $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'User meal not found'
            ], 404);
        }

        $userMeal->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dish removed from diary successfully',
                'diary_note_id' => $diaryNoteId
            ]);
        }        
            
        return redirect()->route('diary');
    }

    private function recount($userId, $diaryNoteId, $request, $userMealId) {
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

        if ($request && $request->expectsJson()) {           
            return response()->json([
                'success' => true,
                'message' => 'Dish added to diary successfully',
                'user_meal_id' => $userMealId,
                'diary_note_id' => $diaryNoteId,
                'nutrition' => $newData
            ]);
        }        

        return redirect()->route('diary');
    }

    private function getData(Request $request) {
        $userId = Auth::user()->id;
        $diaryNoteId = $request->get('diary_note_id') ?? session('diary_note_id');
        $userMealId = $request->get('user_meal_id') ?? session('user_meal_id');

        $dishId = $request->get('dish_id') ?? UserMeal::find($userMealId)->dish_id;
        $dish = Dish::find($dishId);
        
        if ($dish) {
            $ratio = $dish->weight / 100;
            [
                $current_calories, 
                $current_proteins, 
                $current_fats, 
                $current_carbs
            ] = [
                $dish->calories * $ratio,
                $dish->proteins * $ratio,
                $dish->fats * $ratio,
                $dish->carbs * $ratio
            ];
        } else {
            $current_calories = $current_proteins = $current_fats = $current_carbs = 0;
        }

        return [$userId, $diaryNoteId, $dishId, $current_proteins, $current_fats, $current_carbs, $current_calories, $userMealId];
    }
}
