<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Trainer Trainings",
 *     description="Trainer training management endpoints (Trainer User)"
 * )
 * 
 * @OA\Get(
 *     path="/api/trainer/trainings",
 *     summary="Get all trainings",
 *     description="Returns all trainings for authenticated trainer (current and old)",
 *     operationId="getAllTrainings",
 *     tags={"Trainer Trainings"},
 *     security={{"trainerSanctumToken": {}}},
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Trainings retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="current_trainings",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Training")
 *             ),
 *             @OA\Property(
 *                 property="old_trainings",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Training")
 *             ),
 *             @OA\Property(
 *                 property="categories",
 *                 type="object",
 *                 description="Training categories (id => name)",
 *                 @OA\AdditionalProperties(type="string", example="Yoga")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Trainer not found"
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/api/trainer/trainings/new-training",
 *     summary="Get new training form data",
 *     description="Returns categories for creating a new training",
 *     operationId="getNewTrainingForm",
 *     tags={"Trainer Trainings"},
 *     security={{"trainerSanctumToken": {}}},
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Form data retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="categories",
 *                 type="object",
 *                 description="Training categories (id => name)",
 *                 @OA\AdditionalProperties(type="string", example="Yoga")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/api/trainer/trainings/training",
 *     summary="Get training for editing",
 *     description="Returns training data and categories for editing",
 *     operationId="editTraining",
 *     tags={"Trainer Trainings"},
 *     security={{"trainerSanctumToken": {}}},
 *     
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=2),
 *         description="Training ID"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Training retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="training", ref="#/components/schemas/Training"),
 *             @OA\Property(
 *                 property="categories",
 *                 type="object",
 *                 description="Training categories (id => name)",
 *                 @OA\AdditionalProperties(type="string", example="Yoga")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Training not found"
 *     )
 * )
 * 
 * @OA\Post(
 *     path="/api/trainer/trainings/training/create",
 *     summary="Create new training",
 *     description="Creates a new training for authenticated trainer",
 *     operationId="createTraining",
 *     tags={"Trainer Trainings"},
 *     security={{"trainerSanctumToken": {}}},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"name", "time_amount", "price", "start_time", "date", "category_id"},
 *                 @OA\Property(property="name", type="string", maxLength=255, example="Morning Yoga", description="Training name"),
 *                 @OA\Property(property="time_amount", type="integer", minimum=1, example=60, description="Duration in minutes"),
 *                 @OA\Property(property="description", type="string", example="Relaxing morning yoga session", description="Training description", nullable=true),
 *                 @OA\Property(property="price", type="number", format="float", minimum=0, example=30, description="Price in rubles"),
 *                 @OA\Property(property="start_time", type="string", format="time", example="09:00", description="Start time (H:i)"),
 *                 @OA\Property(property="date", type="string", format="date", example="2025-12-25", description="Date (Y-m-d)"),
 *                 @OA\Property(property="category_id", type="integer", example=1, description="Category ID")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=201,
 *         description="Training created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Training created successfully"),
 *             @OA\Property(property="training", ref="#/components/schemas/Training")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Trainer not found"
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="name",
 *                     type="array",
 *                     @OA\Items(type="string", example="The name field is required.")
 *                 )
 *             )
 *         )
 *     )
 * )
 * 
 * @OA\Put(
 *     path="/api/trainer/trainings/training/update",
 *     summary="Update training",
 *     description="Updates an existing training",
 *     operationId="updateTrainerTraining",
 *     tags={"Trainer Trainings"},
 *     security={{"trainerSanctumToken": {}}},
 *     
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=1),
 *         description="Training ID"
 *     ),
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"name", "time_amount", "price", "start_time", "date", "category_id"},
 *                 @OA\Property(property="name", type="string", maxLength=255, example="Evening Yoga"),
 *                 @OA\Property(property="time_amount", type="integer", minimum=1, example=45),
 *                 @OA\Property(property="description", type="string", nullable=true),
 *                 @OA\Property(property="price", type="number", format="float", minimum=0, example=30),
 *                 @OA\Property(property="start_time", type="string", format="time", example="18:00"),
 *                 @OA\Property(property="date", type="string", format="date", example="2025-12-26"),
 *                 @OA\Property(property="category_id", type="integer", example=2)
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Training updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Training updated successfully"),
 *             @OA\Property(property="training", ref="#/components/schemas/Training")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Training not found"
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 * 
 * @OA\Delete(
 *     path="/api/trainer/trainings/training/delete",
 *     summary="Delete training",
 *     description="Deletes a training",
 *     operationId="deleteTraining",
 *     tags={"Trainer Trainings"},
 *     security={{"trainerSanctumToken": {}}},
 *     
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=7),
 *         description="Training ID"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Training deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Training deleted successfully")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Training not found"
 *     )
 * )
 */

class TrainerTrainingController extends Controller
{
    //
}
