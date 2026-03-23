<?php

namespace App\Http\Controllers\TrainerUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\TrainerUser; 
use App\Models\Trainer; 


class TrainerAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
                'unique:restaurant_users,email',
                'unique:trainer_users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ]
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = TrainerUser::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('trainer')->attempt($request->only('email', 'password'));

        return redirect()->route('trainerUser.changeData');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        if (Auth::guard('trainer')->attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            
            return redirect()->route('trainerUser.profile');
        }
        
        return back()->withErrors([
            'email' => 'Неверный email или пароль.',
        ])->onlyInput('email');
    }
    
    public function logout(Request $request)
    {
        Auth::guard('trainer')->logout();
        
        return redirect()->route('trainerUser.login');
    }
}
