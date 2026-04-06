<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Diary",
 *     description="User diary management endpoints"
 * )
 * 
 * @OA\Get(
 *     path="/api/diary",
 *     summary="Get diary for specific date",
 *     description="Returns diary data for a given date. **IMPORTANT**: Save `diary_note_id` and `user_meal_id` from response - it will be required for other diary operations.",
 *     operationId="getDiary",
 *     tags={"Diary"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\Parameter(
 *         name="date",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", format="date", example="2026-04-03"),
 *         description="Date (default: today)"
 *     ),
 *     
 *     @OA\Parameter(
 *         name="movement",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", enum={"back", "forward"}),
 *         description="Navigate to previous/next day with diary entries"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Diary data retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="diary_note_id",
 *                 type="integer",
 *                 example=123,
 *                 description="**SAVE THIS VALUE!** Required for all subsequent diary operations"
 *             ),
 *             @OA\Property(property="date", type="string", format="date", example="2025-12-25"),
 *             @OA\Property(
 *                 property="isset_days",
 *                 type="object",
 *                 @OA\Property(property="dayBefore", type="boolean", example=true),
 *                 @OA\Property(property="dayAfter", type="boolean", example=false)
 *             ),
 *             @OA\Property(property="note_data", ref="#/components/schemas/DiaryNote"),
 *             @OA\Property(
 *                 property="meal_types",
 *                 type="object",
 *                 description="Meal types with Russian names",
 *                 @OA\AdditionalProperties(type="string", example="Завтрак")
 *             ),
 *             @OA\Property(
 *                 property="user_meals",
 *                 type="object",
 *                 description="Meals data grouped by type",
 *                 @OA\AdditionalProperties(ref="#/components/schemas/UserMeal")
 *             ),
 *             @OA\Property(
 *                 property="user_activities",
 *                 type="object",
 *                 description="User activities data",
 *                 nullable=true
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
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/api/diary/item",
 *     summary="Get redirect information for editing",
 *     description="Returns redirect information for editing a product, recipe, or dish",
 *     operationId="redirectToEditItem",
 *     tags={"Diary"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\Parameter(
 *         name="user_meal_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=49),
 *         description="User meal ID"
 *     ),
 *     
 *     @OA\Parameter(
 *         name="item_type",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string", enum={"product", "recepie", "dish"}),
 *         description="Type of item to edit"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Redirect information retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="redirect_to",
 *                 type="string",
 *                 enum={"product", "recepie", "dish"},
 *                 example="product",
 *                 description="Route name to redirect to"
 *             ),
 *             @OA\Property(
 *                 property="params",
 *                 type="object",
 *                 @OA\Property(property="user_meal_id", type="integer", example=456),
 *                 @OA\Property(property="meal_type", type="string", example="breakfast")
 *             ),
 *             @OA\Property(property="item_type", type="string", example="product"),
 *             @OA\Property(property="message", type="string", example="Redirect to edit product")
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
 *         description="User meal not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User meal not found")
 *         )
 *     )
 * )
 * 
 * 
 * @OA\Schema(
 *     schema="MealFoodItem",
 *     type="object",
 *     title="Meal Food Item",
 *     description="Food item in a meal",
 *     
 *     @OA\Property(
 *         property="item",
 *         type="object",
 *         description="The food item object (Product/Recepie/Dish)",
 *         oneOf={
 *             @OA\Schema(ref="#/components/schemas/Product"),
 *             @OA\Schema(ref="#/components/schemas/Recepie"),
 *             @OA\Schema(ref="#/components/schemas/Dish")
 *         }
 *     ),
 *     @OA\Property(property="item_type", type="string", enum={"product", "recepie", "dish"}, example="product"),
 *     @OA\Property(property="user_meal_id", type="integer", example=1, description="User meal ID for editing/deleting"),
 *     @OA\Property(property="amount", type="integer", example=100, description="Amount in grams")
 * )
 * 
 */

class DiaryNoteController extends Controller
{
    //
}
