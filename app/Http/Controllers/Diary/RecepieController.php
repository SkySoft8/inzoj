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
        $previousUrl = url()->previous();
        $recepieId = $this->getData($request)[2];
        $recepie = Recepie::where('id', $recepieId)->first();
        $ingredients = RecepieIngredient::where('recepie_id', $recepieId)->get();

        $isFavorite = $request->get('is_favorite');
        $action = $request->get('action');

        if ($action == 'toggle_favorite') {
            if ($isFavorite == false) {
                UserFavoriteRecepie::create([
                    'user_id' => $this->getData($request)[0],
                    'recepie_id' => $recepieId
                ]);    
            } elseif ($isFavorite == true) {
                UserFavoriteRecepie::where([
                    'user_id' => $this->getData($request)[0],
                    'recepie_id' => $recepieId
                ])->delete();
    
            }
            return redirect()->route('meal');
        }

        $amount = $request->get('amount') ?? null;
        if ($request->has('meal_type')) {
            session(['meal_type' => $request->get('meal_type')]);
        }

        return view('diary.recepie', [
            'recepie' => $recepie,
            'ingredients' => $ingredients,
            'amount' => $amount,
            'url' => $previousUrl,
        ]);

    }

    public function addMealRecepie (Request $request) {
        [$userId, $diaryNoteId, $recepieId, $mealType, $amount] = $this->getData($request);

        $user_meal = UserMeal::create([
            'user_id' => $userId,
            'diary_note_id' => $diaryNoteId,
            'recepie_id' => $recepieId,
            'meal_type' => $mealType,
            'amount' => $amount
        ]);

        return $this->recount($userId, $diaryNoteId);
    }

    public function updateMealRecepie(Request $request) {
        [$userId, $diaryNoteId, $recepieId, $mealType, $amount] = $this->getData($request);

        $user_meal = UserMeal::where('user_id', $userId)
            ->where('diary_note_id', $diaryNoteId)
            ->where('recepie_id', $recepieId)
            ->where('meal_type', $mealType)
            ->update(['amount' => $amount]);

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
        $recepieId = $request->get('recepie_id');
        $mealType = session('meal_type');
        $amount = $request->get('amount');

        return [$userId, $diaryNoteId, $recepieId, $mealType, $amount];
    }
}
