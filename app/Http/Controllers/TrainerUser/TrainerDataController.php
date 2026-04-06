<?php

namespace App\Http\Controllers\TrainerUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\TrainerUser; 

class TrainerDataController extends Controller
{
    public function profile(Request $request) {
        // $trainerUserId = Auth::guard('trainer')->id();
        $trainer = $request->user('sanctum');

        if (!$trainer) {
            $trainer = Auth::guard('trainer')->user();
        }

        if (!$trainer) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trainer not found'
                ], 404);
            }
            abort(404, 'Trainer not found');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'trainer' => $trainer,
            ]);
        }
                    
        return view('trainerUser.profile', ['trainer' => $trainer]);
    }

    public function changeData(Request $request) {
        $trainerUserId = Auth::guard('trainer')->id();
        $trainer = TrainerUser::find($trainerUserId);

        if (!$trainer) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trainer not found'
                ], 404);
            }
            abort(404, 'Trainer not found');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'trainer' => $trainer,
            ]);
        }

        return view('trainerUser.trainerData', ['trainer' => $trainer]);
    }

    public function updateData(Request $request) {
        $trainerUserId = Auth::guard('trainer')->id();
        $trainer = TrainerUser::find($trainerUserId);

        if (!$trainer) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trainer not found'
                ], 404);
            }
            abort(404, 'Trainer not found');
        }

        $newData = $request->except(['_token']);
        $trainer->update($newData);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'trainer' => [
                    'id' => $trainer->id,
                    'email' => $trainer->email,
                    'name' => $trainer->name,
                    'phone' => $trainer->phone,
                    'specialization' => $trainer->specialization,
                    'experience' => $trainer->experience,
                    'bio' => $trainer->bio,
                    'updated_at' => $trainer->updated_at,
                ]
            ]);
        }
        
        return redirect()->route('trainerUser.profile');
    }
}
