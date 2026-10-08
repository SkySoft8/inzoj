<?php

namespace App\Http\Controllers\Sport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\DiaryNote;
use App\Models\Training; 
use App\Models\TrainingCategory; 
use App\Models\TrainerUser;
use App\Models\UserActivity;
use App\Models\UserTraining;
use Illuminate\Database\QueryException;

class SportController extends Controller
{
    public function filter(Request $request) {    
        $trainingCategories = TrainingCategory::pluck('name', 'id');
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'categories' => $trainingCategories
            ]);
        }
        return view('sport.filters', ['trainingCategories' => $trainingCategories]);
    }
        
    public function show(Request $request) {
        $trainingCategories = TrainingCategory::pluck('name', 'id');

        $suitableTrainings = [];
        $categoriesFilter = $request->get('categories', []);
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        if ($request->expectsJson() && (!$fromDate || !$toDate)) {
            return response()->json([
                'success' => false,
                'message' => 'from_date and to_date are required'
            ], 422);
        }        

        $query = Training::whereBetween('date', [$fromDate, $toDate])
            ->whereDoesntHave('userTraining', function ($signup) {
                $signup->where('status', UserTraining::STATUS_ACTIVE);
            })
            ->whereHas('trainerUser', function ($trainerQuery) {
                $trainerQuery->approved();
            })
            ->orderBy('date', 'asc');

        if (!in_array('all', $categoriesFilter) && !empty($categoriesFilter)) {
            $query->whereIn('category_id', $categoriesFilter);
        }

        $suitableTrainings = $query->limit(16)->get();
        $uniqueTrainersId = $suitableTrainings->pluck('trainer_user_id')->unique()->all();
        $trainers = [];
        foreach ($uniqueTrainersId as $id) {
            $trainer = TrainerUser::find($id);
            $trainerName = $trainer->name . ' ' . $trainer->surname;
            $trainers[$id] = $trainerName;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'trainings' => $suitableTrainings,
                'categories' => $trainingCategories,
                'trainers' => $trainers,
                'filters' => [
                    'categories' => $categoriesFilter,
                    'from_date' => $fromDate,
                    'to_date' => $toDate
                ]
            ]);
        }        

        return view('sport.index', [
            'trainings' => $suitableTrainings,
            'categories' => $trainingCategories,
            'trainers' => $trainers,
        ]);
    }

    public function signUp(Request $request) {
        $userId = Auth::user()->id;
        $trainingId = $request->training_id;

        $existing = UserTraining::where('user_id', $userId)
            ->where('training_id', $trainingId)
            ->first();
            
        if ($existing && $existing->status !== UserTraining::STATUS_CANCELLED) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already signed up for this training'
                ], 400);
            }
            return redirect()->back()->with('error', 'Вы уже записаны на эту тренировку');
        }

        $training = Training::with('trainerUser')->find($trainingId);
        if (!$training || !$training->trainerUser || !$training->trainerUser->isApproved()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Training is not available'
                ], 404);
            }
            return redirect()->back()->with('error', 'Тренировка недоступна');
        }

        if ($existing) {
            $existing->update(['status' => UserTraining::STATUS_ACTIVE]);
        } else {
            UserTraining::create([
                'user_id' => $userId,
                'training_id' => $trainingId,
                'status' => UserTraining::STATUS_ACTIVE,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully signed up for training',
                'training_id' => $trainingId
            ]);
        }

        return redirect()->route('userTrainings');
    }

    public function userTrainings(Request $request) {
        $trainingCategories = TrainingCategory::pluck('name', 'id');

        $userId = Auth::user()->id;
        $trainersId = UserTraining::where('user_id', $userId)->pluck('training_id')->toArray();
        $allUserTrainings = Training::whereIn('id', $trainersId)
            ->orderBy('date', 'asc')
            ->get();

        $today = now()->startOfDay()->format('Y-m-d');
        $userTrainings = $allUserTrainings->where('date', '>=', $today);
        $oldTrainings = $allUserTrainings->where('date', '<', $today);
        
        $trainersId = $allUserTrainings->pluck('trainer_user_id')->unique()->toArray();
        $trainers = [];
        foreach ($trainersId as $id) {
            $trainer = TrainerUser::find($id);
            $trainerName = $trainer->name . ' ' . $trainer->surname;
            $trainers[$id] = $trainerName;
        }

        $statuses = $this->signupStatuses($userId);
        $diaryNoteId = $request->get('diary_note_id');
        $addedIds = [];
        if ($request->expectsJson() && $diaryNoteId) {
            $ownsNote = DiaryNote::where('id', $diaryNoteId)->where('user_id', $userId)->exists();
            if (!$ownsNote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diary note not found',
                ], 404);
            }
            $addedIds = UserActivity::where('user_id', $userId)
                ->where('diary_note_id', $diaryNoteId)
                ->whereNotNull('training_id')
                ->pluck('training_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'user_trainings' => $this->trainingsForApi($userTrainings, $diaryNoteId, $addedIds, $statuses),
                'old_trainings' => $this->trainingsForApi($oldTrainings, $diaryNoteId, $addedIds, $statuses),
                'categories' => $trainingCategories,
                'trainers' => $trainers
            ]);
        }        

        $allUserTrainings->each(function ($training) use ($statuses) {
            $training->signup_status = $statuses[(int) $training->id] ?? UserTraining::STATUS_ACTIVE;
        });

        return view('sport.userTrainings', [
            'userTrainings' => $userTrainings,
            'oldTrainings' => $oldTrainings,
            'categories' => $trainingCategories,
            'trainers' => $trainers,
        ]);
    }

    public function revoke(Request $request) {
        $userId = Auth::user()->id;
        $trainingId = $request->training_id;

        $signup = UserTraining::where('user_id', $userId)
            ->where('training_id', $trainingId)
            ->first();

        if (!$signup) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Training signup not found'
                ], 404);
            }
            return redirect()->back()->with('error', 'Запись на тренировку не найдена');
        }

        if ($signup->status === UserTraining::STATUS_CANCELLED) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Training signup already cancelled',
                    'status' => UserTraining::STATUS_CANCELLED,
                    'training_id' => (int) $trainingId,
                ], 409);
            }
            return redirect()->back()->with('error', 'Запись уже отменена');
        }

        $signup->update(['status' => UserTraining::STATUS_CANCELLED]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully unsubscribed from training',
                'training_id' => (int) $trainingId,
                'status' => UserTraining::STATUS_CANCELLED,
            ]);
        }

        return redirect()->route('userTrainings');
    }

    public function addToDiary(Request $request)
    {
        $userId = Auth::user()->id;
        $trainingId = $request->get('training_id');
        $diaryNoteId = $request->get('diary_note_id');

        if (!$trainingId || !$diaryNoteId) {
            return response()->json([
                'success' => false,
                'message' => 'training_id and diary_note_id are required',
            ], 422);
        }

        $note = DiaryNote::where('id', $diaryNoteId)->where('user_id', $userId)->first();
        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Diary note not found',
            ], 404);
        }

        $signedUp = UserTraining::where('user_id', $userId)
            ->where('training_id', $trainingId)
            ->first();
        if (!$signedUp || $signedUp->status !== UserTraining::STATUS_ACTIVE) {
            return response()->json([
                'success' => false,
                'message' => $signedUp ? 'Training signup is cancelled' : 'Training signup not found',
            ], $signedUp ? 409 : 404);
        }

        $training = Training::find($trainingId);
        if (!$training) {
            return response()->json([
                'success' => false,
                'message' => 'Training not found',
            ], 404);
        }

        if ($training->calories === null) {
            return response()->json([
                'success' => false,
                'message' => 'Training calories are not set',
            ], 422);
        }

        $minutes = (int) $training->time_amount;
        if ($minutes < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Training duration is not set',
            ], 422);
        }

        $already = UserActivity::where('user_id', $userId)
            ->where('diary_note_id', $note->id)
            ->where('training_id', $training->id)
            ->exists();
        if ($already) {
            return response()->json([
                'success' => false,
                'message' => 'Training already added to this day',
            ], 409);
        }

        try {
            $userActivity = UserActivity::create([
                'user_id' => $userId,
                'diary_note_id' => $note->id,
                'activity_id' => null,
                'training_id' => $training->id,
                'time_count' => $minutes,
                'time_type' => 'minute',
                'calories' => (int) $training->calories,
            ]);
        } catch (QueryException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062) {
                return response()->json([
                    'success' => false,
                    'message' => 'Training already added to this day',
                ], 409);
            }
            throw $exception;
        }

        $userActivity->load('training');
        $burned = $note->recountBurnedCalories();

        return response()->json([
            'success' => true,
            'message' => 'Training added to diary',
            'user_activity_id' => $userActivity->id,
            'name' => $userActivity->diaryName(),
            'calories' => (int) $userActivity->calories,
            'burned_calories' => $burned,
            'diary_note_id' => $note->id,
            'training_id' => $training->id,
        ]);
    }

    private function signupStatuses(int $userId): array
    {
        $statuses = [];
        $signups = UserTraining::where('user_id', $userId)->get();
        foreach ($signups as $signup) {
            $id = (int) $signup->training_id;
            if (!isset($statuses[$id]) || $signup->status === UserTraining::STATUS_ACTIVE) {
                $statuses[$id] = $signup->status ?: UserTraining::STATUS_ACTIVE;
            }
        }

        return $statuses;
    }

    private function trainingsForApi($trainings, $diaryNoteId, array $addedIds, array $statuses)
    {
        return $trainings->values()->map(function ($training) use ($diaryNoteId, $addedIds, $statuses) {
            $row = $training->toArray();
            $row['status'] = $statuses[(int) $training->id] ?? UserTraining::STATUS_ACTIVE;
            if ($diaryNoteId) {
                $row['added_to_diary'] = in_array((int) $training->id, $addedIds, true);
            }

            return $row;
        });
    }

    public function trainer(Request $request) {
        $trainerId = $request->trainer_id;
        $trainer = TrainerUser::approved()->find($trainerId);

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

        $previousUrl = url()->previous();

        return view('sport.trainer', [
            'trainer' => $trainer,
            'url' => $previousUrl,
        ]);
    }

    public function rating(Request $request) {
        $trainerId = $request->trainer_id;
        $trainer = TrainerUser::approved()->find($trainerId);

        if (!$trainer) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trainer not found'
                ], 404);
            }
            abort(404, 'Trainer not found');
        }        

        $userRating = $request->rating;
        $oldRating = $trainer->rating ?? 0;
        
        if ($oldRating == 0) {
            $trainer->update([
                'rating' => $userRating,
                'rating_count' => 1,
            ]);
        } else {
            $newRating = $userRating + $oldRating;
            $trainer->update([
                'rating' => $newRating,
                'rating_count' => $trainer->rating_count + 1,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rating submitted successfully',
                'trainer_id' => $trainerId,
                'new_rating' => $trainer->rating,
                'rating_count' => $trainer->rating_count
            ]);
        }

        $url = $request->url;

        return view('sport.trainer', [
            'trainer' => $trainer,
            'url' => $url,
        ]);
    }
}
