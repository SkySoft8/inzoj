<?php

namespace App\Http\Controllers\Diary;

use App\Models\DiaryNote;
use App\Models\Activity;
use App\Models\UserFavoriteActivity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function show(Request $request) {
        $user = Auth::user();
        $diaryNoteId = $request->expectsJson() ? $request->get('diary_note_id') : session('diary_note_id');

        if (!$diaryNoteId && $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'diary_note_id is required'
            ], 422);
        }

        $activityType = $request->get('activityType') ?? 'training';

        if ($activityType == 'walking') {
            $note = DiaryNote::find($diaryNoteId);
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'activity_type' => 'walking',
                    'steps' => $note ? $note->current_steps : 0,
                    'diary_note_id' => $diaryNoteId
                ]);
            }
            return view('diary.steps', ['steps' => $note->current_steps]);
        } elseif ($activityType == 'training') {
            $favoriteActiviesId = UserFavoriteActivity::where('user_id', $user->id)
                ->pluck('activity_id')
                ->toArray();
            if (!empty($favoriteActiviesId)) {
                $favorites = Activity::whereIn('id', $favoriteActiviesId)->limit(16)->get();
                $others = Activity::whereNotIn('id', $favoriteActiviesId)->limit(16 - $favorites->count())->get();
                $activities = $favorites->concat($others);
            } else {
                $activities = Activity::limit(16)->get();
            }

            foreach ($activities as $activity) {
                if (in_array($activity->id, $favoriteActiviesId)) {
                    $activity->is_favorite = true;
                } else {
                    $activity->is_favorite = false;
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'activity_type' => 'training',
                    'activities' => $activities,
                    'diary_note_id' => $diaryNoteId
                ]);
            }

            return view('diary.activity', ['activities' => $activities]);
        }
    }

    public function toggleFavorite(Request $request) {
        $userId = Auth::user()->id;
        $isFavorite = $request->get('is_favorite');
        $activityId = $request->get('training_id');

        if ($isFavorite == false) {
            UserFavoriteActivity::create([
                'user_id' => $userId,
                'activity_id' => $activityId
            ]);
        } elseif ($isFavorite == true) {
            UserFavoriteActivity::where([
                'user_id' => $userId,
                'activity_id' => $activityId
            ])->delete();    
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $isFavorite ? 'Activity removed from favorites' : 'Activity added to favorites',
                'is_favorite' => !$isFavorite
            ]);
        }
        return redirect()->route('activity');
    }

    public function updateSteps(Request $request) {
        $diaryNoteId = $request->expectsJson() ? $request->get('diary_note_id') : session('diary_note_id');

        if (!$diaryNoteId && $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'diary_note_id is required'
            ], 422);
        }

        $stepsCount = $request->get('stepsCount');
        $burnedCalories = round($stepsCount * 50 / 1000, 1);

        $diaryNote = DiaryNote::find($diaryNoteId);

        if (!$diaryNote) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diary note not found'
                ], 404);
            }
            abort(404, 'Diary note not found');
        }
        
        $diaryNote->update([
            'burned_calories' => $burnedCalories,
            'current_steps' => $stepsCount
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Steps updated successfully',
            ]);
        }
        
        return redirect()->route('diary');
    }
}
