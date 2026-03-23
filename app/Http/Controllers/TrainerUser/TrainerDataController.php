<?php

namespace App\Http\Controllers\TrainerUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\TrainerUser; 

class TrainerDataController extends Controller
{
    public function profile() {
        $trainerUserId = Auth::guard('trainer')->id();
        $trainer = TrainerUser::find($trainerUserId);

        return view('trainerUser.profile', ['trainer' => $trainer]);
    }

    public function changeData() {
        $trainerUserId = Auth::guard('trainer')->id();
        $trainer = TrainerUser::find($trainerUserId);

        return view('trainerUser.trainerData', ['trainer' => $trainer]);
    }

    public function updateData(Request $request) {
        $newData = $request->except(['_token']);

        $trainerUserId = Auth::guard('trainer')->id();
        TrainerUser::find($trainerUserId)->update($newData);

        return redirect()->route('trainerUser.profile');
    }
}
