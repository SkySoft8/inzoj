<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API authentication endpoints"
 * )
 * 
 * @OA\Post(
 *     path="/api/register",
 *     summary="User registration",
 *     description="Register a new user",
 *     operationId="register",
 *     tags={"Authentication"},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"email", "password", "password_confirmation"},
 *                 @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
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
 *                 @OA\Property(property="email", type="string", example="user@example.com")
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
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="array",
 *                     @OA\Items(type="string", example="The password confirmation does not match.")
 *                 )
 *             )
 *         )
 *     )
 * )
 * 
 * @OA\Post(
 *     path="/api/login",
 *     summary="User login",
 *     description="Authenticate user and return access token",
 *     operationId="login",
 *     tags={"Authentication"},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"email", "password"},
 *                 @OA\Property(property="email", type="string", format="email", example="example@gmail.com"),
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
 *                 @OA\Property(property="email", type="string", example="example@gmail.com"),
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
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="array",
 *                     @OA\Items(type="string", example="The password field is required.")
 *                 )
 *             )
 *         )
 *     )
 * )
 * 
 * @OA\Post(
 *     path="/api/logout",
 *     summary="User logout",
 *     description="Logout user and invalidate access token",
 *     operationId="logout",
 *     tags={"Authentication"},
 *     security={{"userSanctumToken": {}}},
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
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Unauthenticated")
 *         )
 *     )
 * )
 */

class AuthController extends Controller
{
    //
}
