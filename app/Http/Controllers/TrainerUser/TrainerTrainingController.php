<?php

namespace App\Http\Controllers\TrainerUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\TrainingCategory; 
use App\Models\Training; 

class TrainerTrainingController extends Controller
{
    public function showAllTrainings() {
        $trainerUserId = Auth::guard('trainer')->id();
        $allTrainings = Training::where('trainer_user_id', $trainerUserId)->get();

        $today = now()->startOfDay()->format('Y-m-d');
        $currentTrainings = $allTrainings->where('date', '>=', $today);
        $oldTrainings = $allTrainings->where('date', '<', $today);

        $trainingCategories = TrainingCategory::pluck('name', 'id');

        return view('trainerUser.allTrainings', [
            'trainings' => $currentTrainings,
            'oldTrainings' => $oldTrainings,
            'categories' => $trainingCategories,
        ]);
    }

    public function newTraining() {
        $trainingCategories = TrainingCategory::pluck('name', 'id');
        return view('trainerUser.training', ['trainingCategories' => $trainingCategories]);
    }
        
    public function editTraining(Request $request) {
        $trainingId = $request->id;
        $training = Training::find($trainingId);
        $trainingCategories = TrainingCategory::pluck('name', 'id');

        return view('trainerUser.training', [
            'training' => $training,
            'trainingCategories' => $trainingCategories,
        ]);
    }

    public function trainingAction(Request $request) {
        $action = $request->training_action;
        switch ($action) {
            case "create":
                $this->create($request);
                break;
            case "update":
                $this->update($request);
                break;
            case "delete":
                $this->delete($request);
                break;
            default:
                break;
        }

        return redirect()->route('trainerUser.showAllTrainings');
    }

    private function create(Request $request) {
        $newTraining = $request->only([
            'name',
            'time_amount',
            'description',
            'price',
            'start_time',
            'date',
            'category_id'
        ]);

        $trainerUserId = Auth::guard('trainer')->id();
        $newTraining['trainer_user_id'] = $trainerUserId;
        Training::create($newTraining);        
    }

    private function update(Request $request) {
        $trainingId = $request->id;
        $updateTraining = $request->only([
            'name',
            'time_amount',
            'description',
            'price',
            'start_time',
            'date',
            'category_id'
        ]);

        $trainerUserId = Auth::guard('trainer')->id();
        $updateTraining['trainer_user_id'] = $trainerUserId;
        Training::find($trainingId)->update($updateTraining);
    }

    private function delete(Request $request) {
        $trainingId = $request->id;
        Training::find($trainingId)->delete();
    }
}
