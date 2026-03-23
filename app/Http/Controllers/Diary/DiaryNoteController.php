<?php

namespace App\Http\Controllers\Diary;

use App\Models\DiaryNote;
use App\Models\UserMeal;
use App\Models\Product;
use App\Models\Recepies\Recepie;
use App\Models\Restaurants\Dish; 
use App\Models\Activity;
use App\Models\UserActivity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DiaryNoteController extends Controller
{
    public function show(Request $request) {
        $user = Auth::user();
        $date = $request->get('date') ?? Carbon::today();
        $existingDate = DiaryNote::whereDate('diary_date', $date)
            ->where('user_id', $user->id)
            ->first();

        if (!$existingDate) {
            $diaryNote = DiaryNote::create([
                'diary_date' => $date,
                'user_id' => $user->id
            ]);
        }
        
        $issetDays = [
            'dayBefore' => false,
            'dayAfter' => false
        ];
                
        $movement = $request->get('movement') ?? null;
        if ($movement == 'back') {
            $dayBefore = DiaryNote::whereDate('diary_date', '<', $date)
                ->where('user_id', $user->id)
                ->orderBy('diary_date', 'desc')
                ->first();
            $currentDate = $dayBefore->diary_date;
        } elseif ($movement == 'forward') {
            $dayAfter = DiaryNote::whereDate('diary_date', '>', $date)
                ->where('user_id', $user->id)
                ->orderBy('diary_date', 'asc')
                ->first();
            $currentDate = $dayAfter->diary_date;            
        }

        $currentDate = isset($currentDate) ? $currentDate : $date;
        $newDayBefore = DiaryNote::whereDate('diary_date', '<', $currentDate)
            ->where('user_id', $user->id)
            ->orderBy('diary_date', 'desc')
            ->first();
        $newDayAfter = DiaryNote::whereDate('diary_date', '>', $currentDate)
            ->where('user_id', $user->id)
            ->orderBy('diary_date', 'asc')
            ->first();
        $issetDays['dayBefore'] = $newDayBefore ? true : false;
        $issetDays['dayAfter'] = $newDayAfter ? true : false;

        
        $noteData = DiaryNote::where('diary_date', $currentDate)
            ->where('user_id', $user->id)->first();
        if ($user->calories) {
            $leftCalories = $user->calories - $noteData['current_calories'] + $noteData['burned_calories'];
            $noteData['left_calories'] = $leftCalories < 0 ? 'Цель была дсотигнута' : $leftCalories;
        } else {
            $noteData['left_calories'] = null;
        }
        session()->put('diary_note_id', $noteData->id);


        $mealTypes = [
            'breakfast' => 'Завтрак',
            'lunch' => 'Обед',
            'dinner' => 'Ужин',
            'snack' => 'Перекус'
        ];
        
        $userMeals = [];
        $userMealData = [];

        foreach ($mealTypes as $type => $ruType) {
            $userMeals[$type] = UserMeal::where('user_id', $user->id)
                ->where('diary_note_id', $noteData->id)
                ->where('meal_type', $type)
                ->get();
                       
            if ($userMeals[$type] != '[]') {
                $userMealData[$type] = [
                    'proteins' => 0,
                    'fats' => 0,
                    'carbs' => 0,
                    'calories' => 0,
                    'products' => [],
                    'productAmount' => 0
                ];

                foreach ($userMeals[$type] as $currentMeal) {
                    if ($currentMeal->product_id) {
                        $model = Product::class;
                        $itemType = 'product';
                        $id = $currentMeal->product_id;
                    } elseif ($currentMeal->recepie_id) {
                        $model = Recepie::class;
                        $id = $currentMeal->recepie_id;
                        $itemType = 'recepie';
                    } elseif ($currentMeal->dish_id) {
                        $model = Dish::class;
                        $id = $currentMeal->dish_id;
                        $itemType = 'dish';
                    }

                    $item = $model::find($id);
                    $ratio = $currentMeal->amount / 100;
    
                    $userMealData[$type]['proteins'] += round($item->proteins * $ratio, 1);
                    $userMealData[$type]['fats'] += round($item->fats * $ratio, 1);
                    $userMealData[$type]['carbs'] += round($item->carbs * $ratio, 1);
                    $userMealData[$type]['calories'] += round($item->calories * $ratio, 1);
                    $userMealData[$type]['food'][] = [
                        'item' => $item,
                        'item_type' => $itemType,
                        'user_meal_id' => $currentMeal->id,
                        'amount' => $currentMeal->amount
                    ];
                }
            }
        }

        $allUserActivities = UserActivity::where('user_id', $user->id)
            ->where('diary_note_id', $noteData->id)
            ->get();

        if ($allUserActivities != []) {
            $userActivityData = [];
            foreach ($allUserActivities as $userActivity) {
                $training = Activity::find($userActivity->activity_id);
                $trainingName = $training->name;
                $userActivityData[$trainingName] = $userActivity;
            }
        }
        
        if (session('user_meal_id') !== null){
            session()->forget('user_meal_id');
        }
        
        return view('diary.index', [
            'user' => $user,
            'date' => isset($currentDate) ? substr($currentDate, 0, 10) : $date,
            'issetDays' => $issetDays,
            'noteData' => $noteData,
            'mealTypes' => $mealTypes,
            'userMealData' => $userMealData,
            'userActivityData' => $userActivityData ?? null,
        ]);
    }

    public function redirection(Request $request) {
        $userMealId = $request->get('user_meal_id');
        $itemType = $request->get('item_type');
        
        $userMeal = UserMeal::find($userMealId);
        $mealType = $userMeal->meal_type;

        if (session('meal_type') !== null) {
            session(['meal_type' => $mealType]);
        } else {
            session()->put('meal_type', $mealType);
        }

        switch ($itemType) {
            case 'product':
                if (session('user_meal_id') !== null){
                    session(['user_meal_id' => $userMealId]);
                } else {
                    session()->put('user_meal_id', $userMealId);
                }
                return redirect()->route('product');
            case 'recepie':
                return redirect()->route('recepie', ['user_meal_id' => $userMealId]);
            case 'dish':
                return redirect()->route('dish', ['user_meal_id' => $userMealId]);
        }
    }
}
