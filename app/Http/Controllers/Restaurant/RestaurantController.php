<?php

namespace App\Http\Controllers\Restaurant;

use App\Models\Restaurants\Restaurant; 
use App\Models\Restaurants\Dish; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    public function show(Request $request) {
        $restaurantType = $request->get('restaurant_type');
        if (!empty($restaurantType)) {
            $correctRestaurants = Restaurant::where('restaurant_type', $restaurantType)->limit(16)->get();
        } else {
            $correctRestaurants = Restaurant::limit(16)->get();
        }
        return view('restaurant.index', ['restaurants' => $correctRestaurants]);
    }

    public function showMenu(Request $request) {
        $restaurantId = $request->get('restaurant_id');
        $restaurant = Restaurant::find($restaurantId);
        $restaurantDishes = Dish::where('restaurant_id', $restaurantId)->limit(16)->get();

        return view('restaurant.menu', ['restaurant' => $restaurant, 'dishes' => $restaurantDishes]);
    }
}
