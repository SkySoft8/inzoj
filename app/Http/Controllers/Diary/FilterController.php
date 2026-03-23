<?php

namespace App\Http\Controllers\Diary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function show() {
        return view('diary.filters');
    }

    public function filterApply(Request $request) {
        $productsOrRecepies = $request->get('recepies');
        $mealType = $request->get('meal_type') ?? null;
        $component = $request->get('component') ?? null;
        $cookingMethod = $request->get('cooking_method') ?? null;
        $diet = $request->get('diet') ?? null;

        if ($mealType || $component || $cookingMethod || $diet) {
            $issetFilter = true;
        } else {
            $issetFilter = false;
        }

        return view('diary.meal', [
            'productsOrRecepies' => $productsOrRecepies,
            'issetFilter' => $issetFilter,
            'mealType' => $mealType,
            'component' => $component,
            'cookingMethod' => $cookingMethod,
            'diet' => $diet
        ]);
    }
}
