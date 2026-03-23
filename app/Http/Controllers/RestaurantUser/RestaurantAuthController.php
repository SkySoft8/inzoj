<?php

namespace App\Http\Controllers\RestaurantUser;

use App\Models\RestaurantUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RestaurantAuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        if (Auth::guard('restaurant')->attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            
            return redirect()->route('restaurantUser.menu');
        }
        
        return back()->withErrors([
            'email' => 'Неверный email или пароль.',
        ])->onlyInput('email');
    }
    
    public function logout(Request $request)
    {
        Auth::guard('restaurant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('restaurantUser.login');
    }
}
