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
        $userActivityId = $request->get('user_activity_id');
        [$userId, $diaryNoteId, $trainingId, $timeType, $timeCount, $calories] = $this->getData($request, $userActivityId);

        $activity = Activity::find($trainingId);
        if (!$activity && $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Training not found'
            ], 404);
        }

        $adding = $userActivityId !== null ? false : true;
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'activity' => $activity,
                'user_activity_id' => $userActivityId,
                'user_time_type' => $timeType,
                'user_time_count' => $timeCount,
                'user_calories' => $calories,
                'adding' => $adding,
                'diary_note_id' => $diaryNoteId
            ]);
        }

        return view('diary.training', [
            'training' => $activity,
            'userActivityId' => $userActivityId,
            'timeType' => $timeType,
            'timeCount' => $timeCount,
            'calories' => $calories,
            'adding' => $adding    
        ]);
    }

    public function addTraining(Request $request) {
        [$userId, $diaryNoteId, $trainingId, $timeType, $timeCount, $calories] = $this->getData($request);

        $userActivity = UserActivity::create([
            'user_id' => $userId,
            'diary_note_id' => $diaryNoteId,
            'activity_id' => $trainingId,
            'time_count' => $timeCount,
            'time_type' => $timeType,
            'calories' => $calories
        ]);

        $userActivityId = $userActivity->id;

        return $this->recount($userId, $diaryNoteId, true, $request, $userActivityId);
    }

    public function updateTraining(Request $request) {
        [$userId, $diaryNoteId, $trainingId, $timeType, $timeCount, $calories] = $this->getData($request);

        $userActivityId = $request->get('user_activity_id');
        
        UserActivity::find($userActivityId)->update([
            'time_count' => $timeCount,
            'time_type' => $timeType,
            'calories' => $calories,
        ]);

        return $this->recount($userId, $diaryNoteId, false, $request, $userActivityId);
    }

    private function recount($userId, $diaryNoteId, $adding, $request, $userActivityId) {
        $allUserActivities = UserActivity::where('user_id', $userId)
            ->where('diary_note_id', $diaryNoteId)
            ->get();

        $sumCalories = 0;
        foreach ($allUserActivities as $userActivity) {
            $sumCalories += $userActivity->calories;
        }

        $diaryNote = DiaryNote::find($diaryNoteId);
        
        if (!$diaryNote) {
            if ($request && $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diary note not found'
                ], 404);
            }
            return redirect()->route('diary')->with('error', 'Diary note not found');
        }

        $diaryNote->update([
            'burned_calories' => $sumCalories
        ]);

        if ($request && $request->expectsJson()) {           
            return response()->json([
                'success' => true,
                'message' => $adding ? 'Training added successfully' : 'Training updated successfully',
                'user_activity_id' => $userActivityId,
                'burned_calories' => $sumCalories,
                'diary_note_id' => $diaryNoteId
            ]);
        }

        return redirect()->route('diary');
    }

    private function getData(Request $request, int $userActivityId = null) {
        $userId = Auth::user()->id;
        $diaryNoteId = $request->get('diary_note_id') ?? session('diary_note_id');
        $userActivity = UserActivity::find($userActivityId);
        
        if ($userActivity) {
            $trainingId = $userActivity->activity_id;
            $timeCount = $userActivity->time_count;
            $timeType = $userActivity->time_type;
            $calories = $userActivity->calories;
        } else {
            $trainingId = $request->get('training_id') ;
            $timeCount = $request->get('time_count');
            $timeType = $request->get('time_type');
            $calories = $request->get('calories');
        }

        return [$userId, $diaryNoteId, $trainingId, $timeType, $timeCount, $calories, $userActivityId];
    }
}