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
        $dishId = $this->getData($request)[2];
        $dish = Dish::find($dishId);

        $nutrients = [
            $this->getData($request)[3],
            $this->getData($request)[4], 
            $this->getData($request)[5],
            $this->getData($request)[6]
        ];

        $amount = $request->get('amount') ?? null;

        return view('restaurant.dish', [
            'dish' => $dish,
            'nutrients' => $nutrients,
            'amount' => $amount
        ]);
    }

    public function addToDiary(Request $request) {
        [$userId, $diaryNoteId, $dishId] = $this->getData($request);

        $dish = Dish::find($dishId);
        $dishWeight = $dish->weight;
        $mealType = $request->get('meal_type');

        $user_meal = UserMeal::create([
            'user_id' => $userId,
            'diary_note_id' => $diaryNoteId,
            'dish_id' => $dishId,
            'meal_type' => $mealType,
            'amount' => $dishWeight
        ]);

        return $this->recount($userId, $diaryNoteId);
    }

    public function deleteFromDiary(Request $request) {
        [$userId, $diaryNoteId, $dishId] = $this->getData($request);
        $mealType = $request->get('meal_type');

        UserMeal::where('user_id', $userId)
            ->where('diary_note_id', $diaryNoteId)
            ->where('dish_id', $dishId)
            ->where('meal_type', $mealType)
            ->delete();
            
        return redirect()->route('diary');
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
        $dishId = $request->get('dish_id');

        $dish = Dish::find($dishId);
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


        return [$userId, $diaryNoteId, $dishId, $current_proteins, $current_fats, $current_carbs, $current_calories];
    }
}
