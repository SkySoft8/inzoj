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
        $activityType = $request->get('activityType') ?? 'training';

        if ($activityType == 'walking') {
            $note = DiaryNote::find(session('diary_note_id'));
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
            
            return view('diary.activity', ['activities' => $activities]);
        }
    }

    public function updateSteps(Request $request) {
        $stepsCount = $request->get('stepsCount');
        $burnedCalories = round($stepsCount * 50 / 1000, 1);

        DiaryNote::find(session('diary_note_id'))->update([
            'burned_calories' => $burnedCalories,
            'current_steps' => $stepsCount
        ]);

        return redirect()->route('diary');
    }
}
