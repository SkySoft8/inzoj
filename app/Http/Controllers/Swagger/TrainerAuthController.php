<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Trainer Authentication",
 *     description="Trainer authentication endpoints (Trainer User)"
 * )
 * 
 * @OA\Post(
 *     path="/api/trainer/register",
 *     summary="Trainer registration",
 *     description="Register a new trainer",
 *     operationId="trainerRegister",
 *     tags={"Trainer Authentication"},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"email", "password", "password_confirmation"},
 *                 @OA\Property(property="email", type="string", format="email", example="newtrainer@gmail.com"),
 *                 @OA\Property(property="password", type="string", format="password", example="password123"),
 *                 @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Registration successful",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="token", type="string", example="1|abcdefghijklmnopqrstuvwxyz"),
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="email", type="string", example="newtrainer@gmail.com")
 *             )
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
 *                     property="email",
 *                     type="array",
 *                     @OA\Items(type="string", example="The email has already been taken.")
 *                 )
 *             )
 *         )
 *     )
 * )
 * 
 * @OA\Post(
 *     path="/api/trainer/login",
 *     summary="Trainer login",
 *     description="Authenticate trainer and return access token",
 *     operationId="trainerLogin",
 *     tags={"Trainer Authentication"},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"email", "password"},
 *                 @OA\Property(property="email", type="string", format="email", example="trainer@gmail.com"),
 *                 @OA\Property(property="password", type="string", format="password", example="password")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="token", type="string", example="1|abcdefghijklmnopqrstuvwxyz"),
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="email", type="string", example="trainer2@gmail.com"),
 *                 @OA\Property(property="name", type="string", nullable=true)
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Неверный email или пароль.")
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
 *                     property="email",
 *                     type="array",
 *                     @OA\Items(type="string", example="The email field is required.")
 *                 )
 *             )
 *         )
 *     )
 * )
 * 
 * @OA\Post(
 *     path="/api/trainer/logout",
 *     summary="Trainer logout",
 *     description="Logout trainer and invalidate token",
 *     operationId="trainerLogout",
 *     tags={"Trainer Authentication"},
 *     security={{"trainerSanctumToken": {}}},
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Logout successful",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Logged out successfully")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated")
 *         )
 *     )
 * )
 * 
 */

class TrainerAuthController extends Controller
{
    //
}
