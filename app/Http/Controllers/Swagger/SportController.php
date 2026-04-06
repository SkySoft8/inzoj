<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Sport",
 *     description="Sport trainings management"
 * )
 * 
 * @OA\Get(
 *     path="/api/sport/filter",
 *     summary="Get training categories",
 *     description="Returns list of training categories for filtering",
 *     operationId="getSportFilters",
 *     tags={"Sport"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Categories retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="categories",
 *                 type="object",
 *                 description="Training categories (id => name)",
 *                 example={"1": "Yoga", "2": "Pilates", "3": "Crossfit"}
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Get(
 *     path="/api/sport/trainings",
 *     summary="Get available trainings",
 *     description="Returns filtered list of available trainings",
 *     operationId="getTrainings",
 *     tags={"Sport"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\Parameter(
 *         name="categories",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="array", @OA\Items(type="integer")),
 *         description="Category IDs to filter (use 'all' for all categories)",
 *         style="form",
 *         explode=true
 *     ),
 *     
 *     @OA\Parameter(
 *         name="from_date",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", format="date", example="2026-03-01"),
 *         description="Start date for filtering"
 *     ),
 *     
 *     @OA\Parameter(
 *         name="to_date",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", format="date", example="2026-04-13"),
 *         description="End date for filtering"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Trainings retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="trainings", type="array", @OA\Items(ref="#/components/schemas/Training")),
 *             @OA\Property(property="categories", type="object"),
 *             @OA\Property(
 *                 property="trainers",
 *                 type="object",
 *                 description="Trainers (id => name)",
 *                 example={"1": "John Doe", "2": "Jane Smith"}
 *             ),
 *             @OA\Property(
 *                 property="filters",
 *                 type="object",
 *                 @OA\Property(property="categories", type="array", @OA\Items(type="integer")),
 *                 @OA\Property(property="from_date", type="string", format="date"),
 *                 @OA\Property(property="to_date", type="string", format="date")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Missing required parameters",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="from_date and to_date are required")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Post(
 *     path="/api/sport/signup",
 *     summary="Sign up for training",
 *     description="User signs up for a specific training",
 *     operationId="signUpForTraining",
 *     tags={"Sport"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"training_id"},
 *                 @OA\Property(property="training_id", type="integer", example=1, description="Training ID to sign up for")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Successfully signed up",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Successfully signed up for training"),
 *             @OA\Property(property="training_id", type="integer", example=1)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=400,
 *         description="Already signed up",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Already signed up for this training")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="training_id is required")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Get(
 *     path="/api/sport/user-trainings",
 *     summary="Get user's trainings",
 *     description="Returns list of trainings user signed up for",
 *     operationId="getUserTrainings",
 *     tags={"Sport"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\Response(
 *         response=200,
 *         description="User trainings retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="user_trainings", type="array", @OA\Items(ref="#/components/schemas/Training")),
 *             @OA\Property(property="old_trainings", type="array", @OA\Items(ref="#/components/schemas/Training")),
 *             @OA\Property(property="categories", type="object"),
 *             @OA\Property(
 *                 property="trainers",
 *                 type="object",
 *                 description="Trainers (id => name)"
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Delete(
 *     path="/api/sport/revoke",
 *     summary="Cancel training signup",
 *     description="Removes user from training",
 *     operationId="revokeTraining",
 *     tags={"Sport"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"training_id"},
 *                 @OA\Property(property="training_id", type="integer", example=5)
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Successfully unsubscribed",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Successfully unsubscribed from training"),
 *             @OA\Property(property="training_id", type="integer", example=1)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Training signup not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Training signup not found")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="training_id is required")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Get(
 *     path="/api/sport/trainer",
 *     summary="Get trainer info",
 *     description="Returns trainer information",
 *     operationId="getTrainer",
 *     tags={"Sport"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\Parameter(
 *         name="trainer_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=2),
 *         description="Trainer ID"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Trainer info retrieved",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="trainer", ref="#/components/schemas/Trainer")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Trainer not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Trainer not found")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Missing parameter",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="trainer_id is required")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Post(
 *     path="/api/sport/trainer/rating",
 *     summary="Rate a trainer",
 *     description="Submit rating for a trainer",
 *     operationId="rateTrainer",
 *     tags={"Sport"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"trainer_id", "rating"},
 *                 @OA\Property(property="trainer_id", type="integer", example=1, description="Trainer ID"),
 *                 @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5, description="Rating from 1 to 5")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Rating submitted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Rating submitted successfully"),
 *             @OA\Property(property="trainer_id", type="integer", example=1),
 *             @OA\Property(property="new_rating", type="number", format="float", example=4.5),
 *             @OA\Property(property="rating_count", type="integer", example=10)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Trainer not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Trainer not found")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="trainer_id and rating are required")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 */

class SportController extends Controller
{
    //
}
