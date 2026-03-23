<?php

namespace App\Http\Controllers\Sport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Training; 
use App\Models\TrainingCategory; 
use App\Models\TrainerUser;
use App\Models\UserTraining;

class SportController extends Controller
{
    public function filter() {    
        $trainingCategories = TrainingCategory::pluck('name', 'id');
        return view('sport.filters', ['trainingCategories' => $trainingCategories]);
    }
        
    public function show(Request $request) {
        $trainingCategories = TrainingCategory::pluck('name', 'id');

        $suitableTrainings = [];
        $categoriesFilter = $request->categories;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        if (in_array('all', $categoriesFilter) || empty($categoriesFilter)) {
            $suitableTrainings = Training::whereBetween('date', [$fromDate, $toDate])
                ->whereDoesntHave('userTraining')->orderBy('date', 'asc')->limit(16)->get();
            $trainersId = Training::limit(16)->pluck('trainer_user_id')->toArray();
        } else {
            $suitableTrainings = Training::whereBetween('date', [$fromDate, $toDate])
                ->whereDoesntHave('userTraining')->orderBy('date', 'asc')
                ->whereIn('category_id', $categoriesFilter)->limit(16)->get();
            $trainersId = Training::whereIn('category_id', $categoriesFilter)
                ->limit(16)->pluck('trainer_user_id')->toArray();
        }

        $uniqueTrainersId = array_unique($trainersId);
        $trainers = [];
        foreach ($uniqueTrainersId as $id) {
            $trainer = TrainerUser::find($id);
            $trainerName = $trainer->name . ' ' . $trainer->surname;
            $trainers[$id] = $trainerName;
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

        UserTraining::create([
            'user_id' => $userId,
            'training_id' => $trainingId,
        ]);

        return redirect()->route('userTrainings');
    }

    public function userTrainings() {
        $trainingCategories = TrainingCategory::pluck('name', 'id');

        $userId = Auth::user()->id;
        $userTrainingsId = UserTraining::where('user_id', $userId)->pluck('training_id')->toArray();
        $allUserTrainings = Training::whereIn('id', $userTrainingsId)
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

        UserTraining::where('user_id', $userId)
            ->where('training_id', $trainingId)
            ->delete();

        return redirect()->route('userTrainings');
    }

    public function trainer(Request $request) {
        $trainerId = $request->trainer_id;
        $trainer = TrainerUser::find($trainerId);
        $previousUrl = url()->previous();

        return view('sport.trainer', [
            'trainer' => $trainer,
            'url' => $previousUrl,
        ]);
    }

    public function rating(Request $request) {
        $url = $request->url;
        $trainerId = $request->trainer_id;
        $trainer = TrainerUser::find($trainerId);

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

        return view('sport.trainer', [
            'trainer' => $trainer,
            'url' => $url,
        ]);
    }
}
