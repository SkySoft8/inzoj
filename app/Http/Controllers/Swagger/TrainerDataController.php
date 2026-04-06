<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Trainer Profile",
 *     description="Trainer profile management endpoints (Trainer User)"
 * )
 * 
 * @OA\Get(
 *     path="/api/trainer/profile",
 *     summary="Get trainer profile",
 *     description="Returns authenticated trainer's profile data",
 *     operationId="getTrainerProfile",
 *     tags={"Trainer Profile"},
 *     security={{"trainerSanctumToken": {}}},
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Profile retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="trainer",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="email", type="string", example="trainer@example.com"),
 *                 @OA\Property(property="name", type="string", nullable=true, example="John Coach"),
 *                 @OA\Property(property="phone", type="string", nullable=true, example="+1234567890"),
 *                 @OA\Property(property="specialization", type="string", nullable=true, example="Strength Training"),
 *                 @OA\Property(property="experience", type="integer", nullable=true, example=5),
 *                 @OA\Property(property="bio", type="string", nullable=true, example="Experienced fitness coach..."),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated")
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
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/api/trainer/change-data",
 *     summary="Get trainer edit form data",
 *     description="Returns trainer data for editing profile",
 *     operationId="editTrainerData",
 *     tags={"Trainer Profile"},
 *     security={{"trainerSanctumToken": {}}},
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Edit data retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="trainer",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="email", type="string", example="trainer@example.com"),
 *                 @OA\Property(property="name", type="string", nullable=true),
 *                 @OA\Property(property="phone", type="string", nullable=true),
 *                 @OA\Property(property="specialization", type="string", nullable=true),
 *                 @OA\Property(property="experience", type="integer", nullable=true),
 *                 @OA\Property(property="bio", type="string", nullable=true)
 *             ),
 *             @OA\Property(property="editing", type="boolean", example=true)
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
 * @OA\Put(
 *     path="/api/trainer/update-data",
 *     summary="Update trainer profile",
 *     description="Updates authenticated trainer's profile data",
 *     operationId="updateTrainerData",
 *     tags={"Trainer Profile"},
 *     security={{"trainerSanctumToken": {}}},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(property="name", type="string", maxLength=255, example="John Coach", nullable=true),
 *                 @OA\Property(property="phone", type="string", maxLength=20, example="+1234567890", nullable=true),
 *                 @OA\Property(property="specialization", type="string", maxLength=255, example="Strength Training", nullable=true),
 *                 @OA\Property(property="experience", type="integer", minimum=0, maximum=50, example=5, nullable=true),
 *                 @OA\Property(property="bio", type="string", maxLength=1000, example="Experienced fitness coach...", nullable=true)
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Profile updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
 *             @OA\Property(
 *                 property="trainer",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="email", type="string", example="trainer@example.com"),
 *                 @OA\Property(property="name", type="string", nullable=true),
 *                 @OA\Property(property="phone", type="string", nullable=true),
 *                 @OA\Property(property="specialization", type="string", nullable=true),
 *                 @OA\Property(property="experience", type="integer", nullable=true),
 *                 @OA\Property(property="bio", type="string", nullable=true),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated")
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
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="experience",
 *                     type="array",
 *                     @OA\Items(type="string", example="The experience must be between 0 and 50.")
 *                 )
 *             )
 *         )
 *     )
 * )
 */

class TrainerDataController extends Controller
{
    //
}
