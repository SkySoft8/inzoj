<?php

namespace App\Http\Controllers\RestaurantUser;

use App\Models\Restaurants\Restaurant; 
use App\Models\Restaurants\Dish; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantMenuController extends Controller
{
    public function showMenu(Request $request) {
        // $restaurantId = Auth::user()->restaurant_id;
        $restaurant_user = $request->user('sanctum');

        if (!$restaurant_user) {
            $restaurant_user = Auth::guard('restaurant')->user();
        }

        if (!$restaurant_user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant not found'
                ], 404);
            }
            abort(404, 'Restaurant not found');
        }
        
        $restaurantId = $restaurant_user->restaurant_id;
        $restaurant = Restaurant::find($restaurantId);
        $restaurantDishes = Dish::where('restaurant_id', $restaurantId)->limit(16)->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'restaurant' => $restaurant,
                'dishes' => $restaurantDishes,
            ]);
        }

        return view('restaurantUser.menu', ['restaurant' => $restaurant, 'dishes' => $restaurantDishes]);
    }

    public function showDish(Request $request) {
        $dishId = $request->get('dish_id');
        $dish = Dish::find($dishId);
        $ratio = $dish->weight / 100;

        $nutrients = [
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

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'dish' => $dish,
                'nutrients' => [
                    'calories' => $nutrients[0],
                    'proteins' => $nutrients[1],
                    'fats' => $nutrients[2],
                    'carbs' => $nutrients[3],
                ],
            ]);
        }

        return view('restaurantUser.dish', [
            'dish' => $dish,
            'nutrients' => $nutrients,
        ]);
    }

    public function dishAdd(Request $request) {
        $restaurant_user = $request->user('sanctum');

        if (!$restaurant_user) {
            $restaurant_user = Auth::guard('restaurant')->user();
        }

        if (!$restaurant_user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant not found'
                ], 404);
            }
            abort(404, 'Restaurant not found');
        }

        $restaurantId = $restaurant_user->restaurant_id;
        $data = $request->only(['name', 'price', 'weight', 'ingredients', 'proteins', 'fats', 'carbs', 'calories']);
        $data['restaurant_id'] = $restaurantId;
        $data['created_at'] = now();
        $dish = Dish::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dish added successfully',
                'dish' => $dish
            ], 201);
        }
        
        return redirect()->route('restaurantUser.menu');
    }

    public function updateDish(Request $request) {
        $dishId = $request->get('dish_id');
        $dish = Dish::find($dishId);

        if (!$dish) {
            abort(404, 'Dish not found');
        }        
        
        $dish->update(
            $request->only(['name', 'price', 'weight', 'ingredients', 'proteins', 'fats', 'carbs', 'calories'])
        );

        return back();
    }

    public function deleteDish(Request $request) {
        $dishId = $request->get('dish_id');
        $dish = Dish::find($dishId);

        if (!$dish) {
            abort(404, 'Dish not found');
        }        

        $dish->delete();

        return redirect()->route('restaurantUser.menu');
    }


    // For API
    public function update(Request $request)
    {
        $id = $request->get('dish_id');
        $dish = Dish::find($id);
    
        if (!$dish) {
            return response()->json([
                'success' => false,
                'message' => 'Dish not found'
            ], 404);
        }
    
        $dish->update(
            $request->only(['name', 'price', 'weight', 'ingredients', 'proteins', 'fats', 'carbs', 'calories'])
        );
    
        return response()->json([
            'success' => true,
            'message' => 'Dish updated successfully',
            'dish' => $dish
        ]);
    }

    public function delete(Request $request)
    {
        $id = $request->get('dish_id');
        $dish = Dish::find($id);

        if (!$dish) {
            return response()->json([
                'success' => false,
                'message' => 'Dish not found'
            ], 404);
        }

        $dish->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dish deleted successfully'
        ]);
    }
}


