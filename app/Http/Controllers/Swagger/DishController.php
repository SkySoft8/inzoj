<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Restaurants - Dishes",
 *     description="Restaurant dish management in diary"
 * )
 * 
 * @OA\Get(
 *     path="/api/restaurants/dish",
 *     summary="Get dish details",
 *     description="Returns dish information with nutritional values",
 *     operationId="getDish",
 *     tags={"Restaurants - Dishes"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\Parameter(
 *         name="diary_note_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=33),
 *         description="Diary note ID from `GET /api/diary` response"
 *     ),
 *     
 *     @OA\Parameter(
 *         name="dish_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=1),
 *         description="Dish ID"
 *     ),
 *     
 *     @OA\Parameter(
 *         name="user_meal_id",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer", example=42),
 *         description="User meal ID (for editing existing meal)"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Dish retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="dish", ref="#/components/schemas/Dish"),
 *             @OA\Property(
 *                 property="nutrients",
 *                 type="array",
 *                 @OA\Items(type="number", format="float"),
 *                 example={25.5, 15.2, 45.8, 450},
 *                 description="[proteins, fats, carbs, calories]"
 *             ),
 *             @OA\Property(property="adding", type="boolean", example=true),
 *             @OA\Property(property="diary_note_id", type="integer", example=33),
 *             @OA\Property(property="user_meal_id", type="integer", nullable=true)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Dish not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Dish not found")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Missing required parameter",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="diary_note_id is required")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Post(
 *     path="/api/restaurants/dish/add",
 *     summary="Add dish to diary",
 *     description="Adds a restaurant dish to the diary",
 *     operationId="addDishToDiary",
 *     tags={"Restaurants - Dishes"},
 *     security={{"userSanctumToken": {}}},
 * 
 *     @OA\Parameter(
 *         name="diary_note_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=33),
 *         description="Diary note ID from `GET /api/diary` response"
 *     ),
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"dish_id", "meal_type"},
 *                 @OA\Property(property="dish_id", type="integer", example=2, description="**REQUIRED**. Dish ID"),
 *                 @OA\Property(property="meal_type", type="string", enum={"breakfast", "lunch", "dinner", "snack"}, description="**REQUIRED**. Type of meal")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Dish added successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Dish added to diary successfully"),
 *             @OA\Property(property="user_meal_id", type="integer", example=42),
 *             @OA\Property(property="diary_note_id", type="integer", example=33),
 *             @OA\Property(
 *                 property="nutrition",
 *                 type="object",
 *                 @OA\Property(property="calories", type="number", format="float", example=450),
 *                 @OA\Property(property="proteins", type="number", format="float", example=25.5),
 *                 @OA\Property(property="fats", type="number", format="float", example=15.2),
 *                 @OA\Property(property="carbs", type="number", format="float", example=45.8)
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Dish not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Dish not found")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="dish_id is required")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Delete(
 *     path="/api/restaurants/dish/delete",
 *     summary="Delete dish from diary",
 *     description="Removes a dish from the diary",
 *     operationId="deleteDishFromDiary",
 *     tags={"Restaurants - Dishes"},
 *     security={{"userSanctumToken": {}}},
 * 
 *     @OA\Parameter(
 *         name="diary_note_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=33),
 *         description="Diary note ID from `GET /api/diary` response"
 *     ),
 *     
 *     @OA\Parameter(
 *         name="user_meal_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=42),
 *         description="User meal ID to delete"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Dish removed successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Dish removed from diary successfully"),
 *             @OA\Property(property="diary_note_id", type="integer", example=33)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="User meal not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User meal not found")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Missing required parameter",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="user_meal_id is required")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 */

class DishController extends Controller
{
    //
}
