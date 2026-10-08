<?php

namespace App\Http\Controllers\Diary;

use App\Models\Activity;
use App\Models\UserActivity;
use App\Models\UserActivityStat;
use App\Models\DiaryNote;
use App\Models\UserFavoriteActivity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function show(Request $request)
    {
        $userActivityId = $request->get('user_activity_id');
        [$userId, $diaryNoteId, $trainingId, $timeType, $timeCount, $calories] = $this->getData($request, $userActivityId);

        $user = Auth::user();
        $activity = Activity::visibleTo($user)->find($trainingId);
        if (!$activity && $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Training not found',
            ], 404);
        }

        $favoriteIds = UserFavoriteActivity::where('user_id', $userId)->pluck('activity_id')->all();
        $adding = $userActivityId !== null ? false : true;
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'activity' => $activity ? $activity->toApiArray($user, in_array((int) $activity->id, $favoriteIds, true)) : null,
                'user_activity_id' => $userActivityId,
                'user_time_type' => $timeType,
                'user_time_count' => $timeCount,
                'user_calories' => $calories,
                'adding' => $adding,
                'diary_note_id' => $diaryNoteId,
            ]);
        }

        return view('diary.training', [
            'training' => $activity,
            'userActivityId' => $userActivityId,
            'timeType' => $timeType,
            'timeCount' => $timeCount,
            'calories' => $calories,
            'adding' => $adding,
        ]);
    }

    public function createActivity(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'time' => 'nullable|string|max:50',
            'calories' => 'nullable|integer|min:0|max:2000',
            'location_type' => 'nullable|in:'.implode(',', Activity::LOCATIONS),
            'category' => 'nullable|string|max:50',
        ]);

        $activity = Activity::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'time' => $validated['time'] ?? '10 мин',
            'calories' => $validated['calories'] ?? 50,
            'location_type' => $validated['location_type'] ?? Activity::LOCATION_ANY,
            'category' => $validated['category'] ?? null,
            'video_url' => null,
            'is_premium' => false,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Custom activity created',
                'activity' => $activity->toApiArray($user),
            ], 201);
        }

        return redirect()->route('activity');
    }

    public function addTraining(Request $request)
    {
        [$userId, $diaryNoteId, $trainingId, $timeType, $timeCount] = $this->getData($request);

        if (!$diaryNoteId || !DiaryNote::where('id', $diaryNoteId)->where('user_id', $userId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Diary note not found',
            ], 404);
        }

        $activity = Activity::visibleTo(Auth::user())->find($trainingId);
        if (!$activity) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Training not found',
                ], 404);
            }
            abort(404, 'Training not found');
        }

        $calories = $this->caloriesForRequest($activity, $timeCount, $timeType);
        if ($calories instanceof \Illuminate\Http\JsonResponse) {
            return $calories;
        }

        $userActivity = UserActivity::create([
            'user_id' => $userId,
            'diary_note_id' => $diaryNoteId,
            'activity_id' => $trainingId,
            'time_count' => $timeCount,
            'time_type' => $timeType,
            'calories' => $calories,
        ]);

        $this->rememberActivity($userId, (int) $trainingId);

        return $this->recount($diaryNoteId, true, $request, $userActivity->id);
    }

    public function updateTraining(Request $request)
    {
        [$userId, $diaryNoteId, $trainingId, $timeType, $timeCount] = $this->getData($request);

        $userActivityId = $request->get('user_activity_id');
        $userActivity = UserActivity::where('user_id', $userId)->find($userActivityId);

        if (!$userActivity) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User activity not found',
                ], 404);
            }
            abort(404, 'User activity not found');
        }

        $activity = Activity::visibleTo(Auth::user())->find($userActivity->activity_id);
        $calories = $activity
            ? $this->caloriesForRequest($activity, $timeCount, $timeType)
            : response()->json(['success' => false, 'message' => 'Training not found'], 404);
        if ($calories instanceof \Illuminate\Http\JsonResponse) {
            return $calories;
        }

        $userActivity->update([
            'time_count' => $timeCount,
            'time_type' => $timeType,
            'calories' => $calories,
        ]);

        return $this->recount($userActivity->diary_note_id, false, $request, $userActivity->id);
    }

    public function deleteTraining(Request $request, $id)
    {
        $userActivity = UserActivity::where('user_id', Auth::id())->find($id);

        if (!$userActivity) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User activity not found',
                ], 404);
            }
            abort(404, 'User activity not found');
        }

        $diaryNoteId = $userActivity->diary_note_id;
        $userActivity->delete();

        return $this->recount($diaryNoteId, false, $request, null, true);
    }

    private function recount($diaryNoteId, $adding, $request, $userActivityId, $deleted = false)
    {
        $diaryNote = DiaryNote::find($diaryNoteId);

        if (!$diaryNote) {
            if ($request && $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diary note not found',
                ], 404);
            }
            return redirect()->route('diary')->with('error', 'Diary note not found');
        }

        $sumCalories = $diaryNote->recountBurnedCalories();

        if ($request && $request->expectsJson()) {
            $message = 'Training updated successfully';
            if ($deleted) {
                $message = 'Training deleted successfully';
            } elseif ($adding) {
                $message = 'Training added successfully';
            }

            $saved = $userActivityId ? UserActivity::find($userActivityId) : null;

            return response()->json([
                'success' => true,
                'message' => $message,
                'user_activity_id' => $userActivityId,
                'time_count' => $saved?->time_count,
                'time_type' => $saved?->time_type,
                'calories' => $saved?->calories,
                'burned_calories' => $sumCalories,
                'diary_note_id' => $diaryNoteId,
            ]);
        }

        return redirect()->route('diary');
    }

    private function getData(Request $request, $userActivityId = null)
    {
        $userId = Auth::user()->id;
        $diaryNoteId = $request->get('diary_note_id') ?? session('diary_note_id');
        $userActivity = $userActivityId ? UserActivity::where('user_id', $userId)->find($userActivityId) : null;

        if ($userActivity) {
            $trainingId = $userActivity->activity_id;
            $timeCount = $userActivity->time_count;
            $timeType = $userActivity->time_type;
            $calories = $userActivity->calories;
        } else {
            $trainingId = $request->get('training_id');
            $timeCount = $request->get('time_count');
            $timeType = $request->get('time_type');
            $calories = $request->get('calories');
        }

        return [$userId, $diaryNoteId, $trainingId, $timeType, $timeCount, $calories, $userActivityId];
    }

    private function caloriesForRequest(Activity $activity, $timeCount, $timeType)
    {
        if (!in_array($timeType, ['minute', 'hour'], true) || !is_numeric($timeCount) || (int) $timeCount < 1) {
            return response()->json([
                'success' => false,
                'message' => 'time_count and time_type are required',
            ], 422);
        }

        try {
            return $activity->caloriesFor((int) $timeCount, $timeType);
        } catch (\InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    private function rememberActivity(int $userId, int $activityId): void
    {
        $stat = UserActivityStat::firstOrCreate(
            ['user_id' => $userId, 'activity_id' => $activityId],
            ['times' => 0]
        );
        $stat->increment('times');
        $stat->update(['last_added_at' => now()]);
    }
}
