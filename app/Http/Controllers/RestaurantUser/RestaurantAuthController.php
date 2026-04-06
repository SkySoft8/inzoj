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
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }
        
        if (Auth::guard('restaurant')->attempt($request->only('email', 'password'))) {
            $user = Auth::guard('restaurant')->user();
            
            $user->tokens()->delete();
            $token = $user->createToken('restaurant_auth_token')->plainTextToken;

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name ?? null,
                    ]
                ]);
            }

            $request->session()->regenerate();
            return redirect()->route('restaurantUser.menu');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный email или пароль.'
            ], 401);
        }

        return back()->withErrors([
            'email' => 'Неверный email или пароль.',
        ])->onlyInput('email');
    }
    
    public function logout(Request $request)
    {
        if ($request->expectsJson()) {
            if ($request->user('sanctum')) {
                $request->user('sanctum')->currentAccessToken()->delete();
            }
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        }
        
        if (Auth::guard('restaurant')->check()) {
            $user = Auth::guard('restaurant')->user();
            $user->tokens()->delete();
        }
                
        Auth::guard('restaurant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('restaurantUser.login');
    }
}
