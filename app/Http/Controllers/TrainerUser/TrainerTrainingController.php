<?php

namespace App\Http\Controllers\TrainerUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\TrainingCategory; 
use App\Models\Training; 

class TrainerTrainingController extends Controller
{
    public function showAllTrainings(Request $request) {
        $trainer = $this->getAuthenticatedTrainer($request);
        
        if (!$trainer) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trainer not found'
                ], 404);
            }
            abort(404, 'Trainer not found');
        }
        
        $trainerUserId = $trainer->id;
        $allTrainings = Training::where('trainer_user_id', $trainerUserId)->get();

        $today = now()->startOfDay()->format('Y-m-d');
        $currentTrainings = $allTrainings->where('date', '>=', $today);
        $oldTrainings = $allTrainings->where('date', '<', $today);

        $trainingCategories = TrainingCategory::pluck('name', 'id');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'current_trainings' => $currentTrainings->values(),
                'old_trainings' => $oldTrainings->values(),
                'categories' => $trainingCategories,
            ]);
        }

        return view('trainerUser.allTrainings', [
            'trainings' => $currentTrainings,
            'oldTrainings' => $oldTrainings,
            'categories' => $trainingCategories,
        ]);
    }

    public function newTraining(Request $request) {
        $trainingCategories = TrainingCategory::pluck('name', 'id');
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'categories' => $trainingCategories,
            ]);
        }
        return view('trainerUser.training', ['trainingCategories' => $trainingCategories]);
    }
        
    public function editTraining(Request $request) {
        $trainingId = $request->id;
        $training = Training::find($trainingId);

        if (!$training) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Training not found'
                ], 404);
            }
            abort(404, 'Training not found');
        }

        $trainingCategories = TrainingCategory::pluck('name', 'id');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'training' => $training,
                'categories' => $trainingCategories,
            ]);
        }

        return view('trainerUser.training', [
            'training' => $training,
            'trainingCategories' => $trainingCategories,
        ]);
    }


    // For WEB
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


    // For API
    public function createTraining(Request $request)
    {
        $trainer = $this->getAuthenticatedTrainer($request);
        
        if (!$trainer) {
            return response()->json([
                'success' => false,
                'message' => 'Trainer not found'
            ], 404);
        }

        $newTraining = $request->only([
            'name',
            'time_amount',
            'description',
            'price',
            'start_time',
            'date',
            'category_id'
        ]);

        $newTraining['trainer_user_id'] = $trainer->id;
        $training = Training::create($newTraining);

        return response()->json([
            'success' => true,
            'message' => 'Training created successfully',
            'training' => $training
        ], 201);
    }

    public function updateTraining(Request $request)
    {
        $id = $request->get('id');
        $training = Training::find($id);

        if (!$training) {
            return response()->json([
                'success' => false,
                'message' => 'Training not found'
            ], 404);
        }

        $updateTraining = $request->only([
            'name',
            'time_amount',
            'description',
            'price',
            'start_time',
            'date',
            'category_id'
        ]);

        $training->update($updateTraining);

        return response()->json([
            'success' => true,
            'message' => 'Training updated successfully',
            'training' => $training
        ]);
    }

    public function deleteTraining(Request $request)
    {
        $id = $request->get('id');
        $training = Training::find($id);

        if (!$training) {
            return response()->json([
                'success' => false,
                'message' => 'Training not found'
            ], 404);
        }

        $training->delete();

        return response()->json([
            'success' => true,
            'message' => 'Training deleted successfully'
        ]);
    }
    

    private function getAuthenticatedTrainer(Request $request)
    {
        $trainer = $request->user('sanctum');
        
        if (!$trainer) {
            $trainer = Auth::guard('trainer')->user();
        }
        
        return $trainer;
    }
}
