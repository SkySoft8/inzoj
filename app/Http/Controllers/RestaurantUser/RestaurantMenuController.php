<?php

namespace App\Http\Controllers\RestaurantUser;

use App\Models\Restaurants\Restaurant; 
use App\Models\Restaurants\Dish; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantMenuController extends Controller
{
    public function showMenu() {
        $restaurantId = Auth::user()->restaurant_id;
        $restaurant = Restaurant::find($restaurantId);
        $restaurantDishes = Dish::where('restaurant_id', $restaurantId)->limit(16)->get();

        return view('restaurantUser.menu', ['restaurant' => $restaurant, 'dishes' => $restaurantDishes]);
    }

    public function dishAdd(Request $request) {
        $restaurantId = Auth::user()->restaurant_id;

        $data = $request->only(['name', 'price', 'weight', 'ingredients', 'proteins', 'fats', 'carbs', 'calories']);
        $data['restaurant_id'] = $restaurantId;
        $data['created_at'] = now();
        Dish::create($data);

        return redirect()->route('restaurantUser.menu');
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

        return view('restaurantUser.dish', [
            'dish' => $dish,
            'nutrients' => $nutrients,
        ]);
    }

    public function dishAction(Request $request) {
        $action = $request->get('action');
        $dishId = $request->get('dish_id');

        if ($action === 'update') {
            Dish::find($dishId)->update(
                $request->only(['name', 'price', 'weight', 'ingredients', 'proteins', 'fats', 'carbs', 'calories'])
            );

            return back();
        } elseif ($action === 'delete') {
            Dish::find($dishId)->delete();

            return redirect()->route('restaurantUser.menu');
        }
    }
}
