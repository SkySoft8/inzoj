<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        if ($user->profile_photo_path) {
            Storage::disk('local')->delete($user->profile_photo_path);
        }

        $path = $request->file('photo')->store('profile-photos/'.$user->id, 'local');
        $user->update(['profile_photo_path' => $path]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated',
                'profile_photo_url' => $user->fresh()->profile_photo_url,
            ]);
        }

        return redirect()->back()->with('success', 'Фото профиля обновлено');
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        if ($user->profile_photo_path) {
            Storage::disk('local')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile photo deleted',
            ]);
        }

        return redirect()->back()->with('success', 'Фото профиля удалено');
    }

    public function show(Request $request)
    {
        $user = Auth::user();

        if (!$user->profile_photo_path || !Storage::disk('local')->exists($user->profile_photo_path)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profile photo not found',
                ], 404);
            }

            abort(404);
        }

        return Storage::disk('local')->response($user->profile_photo_path);
    }
}
