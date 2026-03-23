<?php

namespace App\Http\Controllers\Diary;

use App\Models\Activity; 
use App\Models\UserActivity; 
use App\Models\DiaryNote;
use App\Models\UserFavoriteActivity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function show(Request $request) {
        $trainingId = $this->getData($request)[2];
        $training = Activity::find($trainingId);
        $isFavorite = $request->get('is_favorite');
        $action = $request->get('action');

        if ($action == 'toggle_favorite') {
            if ($isFavorite == false) {
                UserFavoriteActivity::create([
                    'user_id' => $this->getData($request)[0],
                    'activity_id' => $trainingId
                ]);    
            } elseif ($isFavorite == true) {
                UserFavoriteActivity::where([
                    'user_id' => $this->getData($request)[0],
                    'activity_id' => $trainingId
                ])->delete();
    
            }
            return redirect()->route('activity');
        }

        return view('diary.training', ['training' => $training]);
    }

    public function addTraining (Request $request) {
        [$userId, $diaryNoteId, $activityId, $timeType, $timeCount, $calories] = $this->getData($request);

        $user_activity = UserActivity::create([
            'user_id' => $userId,
            'diary_note_id' => $diaryNoteId,
            'activity_id' => $activityId,
            'time_count' => $timeCount,
            'time_type' => $timeType,
            'calories' => $calories
        ]);

        return $this->recount($userId, $diaryNoteId);
    }

    private function recount($userId, $diaryNoteId) {
        $allUserActivities = UserActivity::where('user_id', $userId)
            ->where('diary_note_id', $diaryNoteId)
            ->get();

        $sumCalories = 0;
        foreach ($allUserActivities as $userActivity) {
            $sumCalories += $userActivity->calories;
        }

        DiaryNote::find($diaryNoteId)->update([
            'burned_calories' => $sumCalories
        ]);

        return redirect()->route('diary');
    }

    private function getData(Request $request) {
        $userId = Auth::user()->id;
        $diaryNoteId = session('diary_note_id');
        $activityId = $request->get('training_id');
        $timeCount = $request->get('time_count');
        $timeType = $request->get('time_type');
        $calories = $request->get('calories');

        return [$userId, $diaryNoteId, $activityId, $timeType, $timeCount, $calories];
    }
}