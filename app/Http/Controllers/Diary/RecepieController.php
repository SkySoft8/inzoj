<?php

namespace App\Http\Controllers\Diary;

use App\Models\Recepies\Recepie;
use App\Models\Recepies\RecepieIngredient;
use App\Models\UserFavoriteRecepie;
use App\Models\UserRecepie;
use App\Models\UserRecepieIngridient;

use App\Models\Product;
use App\Models\Restaurants\Dish; 
use App\Models\UserMeal;
use App\Models\DiaryNote;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DiaryBrowse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecepieController extends Controller
{
    public function show (Request $request) {
        [$userId, $diaryNoteId, $recepieId, $mealType, $amount, $userMealId] = $this->getData($request);        
        $previousUrl = url()->previous();

        $isUserRecepie = $request->is_user_recepie ?? null;
        if ($isUserRecepie !== null) {
            $recepie = UserRecepie::where('id', $recepieId)->first();
            if ($recepie && $recepie->user_id !== Auth::id() && !$recepie->isApproved()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Recipe is not available'
                    ], 403);
                }
                abort(403);
            }
        } else {
            $recepie = Recepie::where('id', $recepieId)->first();
        }

        if (!$recepie && $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Recipe not found'
            ], 404);
        }

        if ($isUserRecepie !== null) {
            $ingredientIds = UserRecepieIngridient::where('user_recepie_id', $recepieId)
                ->pluck('ingredient_id')
                ->toArray();
            $ingredients = RecepieIngredient::whereIn('id', $ingredientIds)->get();
        } else {
            $ingredients = RecepieIngredient::where('recepie_id', $recepieId)->get();
        }
        if ($request->expectsJson()) {
            if ($recepie instanceof UserRecepie) {
                $recepie->load('items.product');
                app(DiaryBrowse::class)->rememberRecipe(Auth::id(), [
                    'meal_type' => $recepie->meal_types ?? [],
                    'component' => $recepie->components ?? [],
                    'cooking_method' => $recepie->cooking_methods ?? [],
                    'diet' => $recepie->diets ?? [],
                ]);
            } else {
                app(DiaryBrowse::class)->rememberRecipe(Auth::id(), [
                    'meal_type' => \App\Models\Recepies\RecepieMealType::where('recepie_id', $recepie->id)->pluck('meal_type')->all(),
                    'component' => \App\Models\Recepies\RecepieComponent::where('recepie_id', $recepie->id)->pluck('component')->all(),
                    'cooking_method' => \App\Models\Recepies\RecepieCookingMethod::where('recepie_id', $recepie->id)->pluck('cooking_method')->all(),
                    'diet' => \App\Models\Recepies\RecepieDiet::where('recepie_id', $recepie->id)->pluck('diet')->all(),
                ]);
            }

            return response()->json([
                'success' => true,
                'recepie' => $recepie,
                'ingredients' => $ingredients,
                'items' => $recepie instanceof UserRecepie ? $recepie->items : [],
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

        $isUserRecepie = $request->boolean('is_user_recepie') || $request->filled('user_recepie_id');
        $userRecepieId = null;

        if ($isUserRecepie) {
            $userRecepieId = $request->get('user_recepie_id') ?: $recepieId;
            $userRecepie = UserRecepie::with('items')->find($userRecepieId);
            if (!$userRecepie || !$userRecepie->isApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recipe is not available',
                ], 403);
            }
            $recepieId = null;
            $amount = $amount ?: $userRecepie->portionGrams();
        }

        if (!$diaryNoteId || !DiaryNote::where('id', $diaryNoteId)->where('user_id', $userId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Diary note not found. Take diary_note_id from GET /api/diary',
            ], 404);
        }

        $userMeal = UserMeal::create([
            'user_id' => $userId,
            'diary_note_id' => $diaryNoteId,
            'recepie_id' => $recepieId,
            'user_recepie_id' => $userRecepieId,
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

    public function showUserRecepieForm(Request $request) {
        $categories = app(DiaryBrowse::class)->filters((int) Auth::id());
        unset($categories['popular']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'categories' => $categories,
            ]);
        }

        return view('diary.userRecepie', ['categories' => $categories]);
    }

    public function createUserRecepie(Request $request) {
        $this->decodeJsonFields($request);

        if ($request->filled('ingredients')) {
            return $this->createFromProducts($request);
        }

        $userId = Auth::user()->id;
        $name = $request->get('name');
        $calories = $request->get('calories');
        $proteins = $request->get('proteins');
        $fats = $request->get('fats');
        $carbs = $request->get('carbs');
        $steps = $request->input('steps');
        if (is_string($steps)) {
            $steps = trim($steps) === '' ? [] : [trim($steps)];
        }
        if (!is_array($steps) || $steps === []) {
            $legacy = trim((string) $request->input('instructions', ''));
            $steps = $legacy !== '' ? [$legacy] : null;
        }

        $recepie = UserRecepie::create([
            'user_id' => $userId,
            'name' => $name,
            'steps' => $steps,
            'meal_types' => $this->selectedTags($request, 'meal_types', 'meal_type'),
            'components' => $this->selectedTags($request, 'components', 'component'),
            'cooking_methods' => $this->selectedTags($request, 'cooking_methods', 'cooking_method'),
            'diets' => $this->selectedTags($request, 'diets', 'diet'),
            'calories' => $calories,
            'proteins' =>  $proteins,
            'fats' => $fats,
            'carbs' => $carbs,
            'moderation_status' => UserRecepie::MODERATION_PENDING,
        ]);

        $ingredientIds = $request->get('ingredient_ids', []);
        $userRecepieId = $recepie->id;
        if (is_array($ingredientIds) && count($ingredientIds) > 0) {
            $data = array_map(function ($ingredientId) use ($userRecepieId) {
                return [
                    'user_recepie_id' => $userRecepieId,
                    'ingredient_id' => $ingredientId
                ];
            }, $ingredientIds);
            
            UserRecepieIngridient::insert($data);
        }

        if ($request && $request->expectsJson()) {           
            return response()->json([
                'success' => true,
                'message' => 'Recipe submitted for moderation',
                'recepie' => $recepie,
            ], 201);
        }

        return redirect()->route('meal');
    }

    private function createFromProducts(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'portions' => 'nullable|integer|min:1|max:50',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.product_id' => 'required|integer|exists:products,id',
            'ingredients.*.grams' => 'required|integer|min:1|max:5000',
            'steps' => 'nullable|array',
            'steps.*' => 'string|max:500',
            'meal_types' => 'nullable|array',
            'meal_types.*' => 'in:breakfast,lunch,dinner,snack',
            'components' => 'nullable|array',
            'components.*' => 'in:poultry,meat,fish,vegetables,fruits,sweet',
            'cooking_methods' => 'nullable|array',
            'cooking_methods.*' => 'in:boiled,steamed,fried,stew,baked,basic',
            'diets' => 'nullable|array',
            'diets.*' => 'in:vegetarian,vegan,low_fat,lots_of_fiber,low_carb,keto_diet,high_protein,lactose_free',
            'photo' => 'nullable|image|max:5120',
        ]);

        $portions = (int) ($validated['portions'] ?? 1);
        $nutrition = UserRecepie::nutritionFromProducts($validated['ingredients'], $portions);
        $steps = array_values($validated['steps'] ?? []);
        $image = null;
        if ($request->hasFile('photo')) {
            $image = $request->file('photo')->store('user-recipes/'.Auth::id(), 'public');
        }

        $recepie = DB::transaction(function () use ($validated, $portions, $nutrition, $steps, $image) {
            $recepie = UserRecepie::create([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'steps' => $steps ?: null,
                'image' => $image,
                'calories' => $nutrition['calories'],
                'proteins' => $nutrition['proteins'],
                'fats' => $nutrition['fats'],
                'carbs' => $nutrition['carbs'],
                'portions' => $portions,
                'meal_types' => array_values($validated['meal_types'] ?? []),
                'components' => array_values($validated['components'] ?? []),
                'cooking_methods' => array_values($validated['cooking_methods'] ?? []),
                'diets' => array_values($validated['diets'] ?? []),
                'moderation_status' => UserRecepie::MODERATION_PENDING,
            ]);

            foreach ($nutrition['lines'] as $line) {
                $recepie->items()->create($line);
            }

            return $recepie;
        });

        $recepie->load('items.product');

        return response()->json([
            'success' => true,
            'message' => 'Recipe submitted for moderation',
            'recepie' => $recepie,
        ], 201);
    }

    private function selectedTags(Request $request, string $field, string $group): array
    {
        $values = $request->input($field, []);
        if (!is_array($values)) {
            $values = [$values];
        }

        return array_values(array_intersect($values, DiaryBrowse::FILTERS[$group]));
    }

    private function decodeJsonFields(Request $request): void
    {
        foreach (['ingredients', 'steps', 'meal_types', 'components', 'cooking_methods', 'diets'] as $field) {
            $value = $request->input($field);
            if (!is_string($value)) {
                continue;
            }

            $value = trim($value);
            $decoded = json_decode($value, true);
            if (!is_array($decoded)) {
                $decoded = json_decode(stripslashes($value), true);
            }
            if (is_array($decoded)) {
                $request->merge([$field => $decoded]);
            }
        }
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
