<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Diary - Recipes",
 *     description="Recipe management in diary meals"
 * )
 * 
 * @OA\Get(
 *     path="/api/diary/meal/recepie",
 *     summary="Get recipe details",
 *     description="Recipe card. For a user recipe send is_user_recepie=1. Items with grams are returned for a user recipe. Catalog cooking text is in steps.",
 *     operationId="getRecepie",
 *     tags={"Diary - Recipes"},
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
 *         name="recepie_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=2),
 *         description="Recipe ID"
 *     ),
 *     
 *     @OA\Parameter(
 *         name="is_user_recepie",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="boolean", example=true),
 *         description="Pass true to open a user recipe"
 *     ),
 *
 *     @OA\Parameter(
 *         name="meal_type",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", enum={"breakfast", "lunch", "dinner", "snack"}),
 *         description="Type of meal (required when adding new recipe)"
 *     ),
 * 
 *     @OA\Parameter(
 *         name="user_meal_id",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer", example=45),
 *         description="User meal ID (for editing existing meal)"
 *     ),
 *          
 *     @OA\Response(
 *         response=200,
 *         description="Recipe retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="recepie", ref="#/components/schemas/UserRecepie"),
 *             @OA\Property(
 *                 property="ingredients",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/RecepieIngredient")
 *             ),
 *             @OA\Property(property="items", type="array", description="User recipe lines: product and grams", @OA\Items(type="object")),
 *             @OA\Property(property="amount", type="integer", example=100),
 *             @OA\Property(property="diary_note_id", type="integer", example=33),
 *             @OA\Property(property="user_meal_id", type="integer", nullable=true)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Recipe not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Recipe not found")
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
 *     path="/api/diary/meal/recepie/add",
 *     summary="Add recipe to meal",
 *     description="Writes the recipe into the opened meal. Catalog: recepie_id. User recipe: is_user_recepie true and user_recepie_id. amount is grams and may be omitted for a user recipe; then one portion is used. The recipe must already be approved.",
 *     operationId="addRecepieToMeal",
 *     tags={"Diary - Recipes"},
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
 *                 required={"meal_type"},
 *                 @OA\Property(property="recepie_id", type="integer", example=1, description="Catalog recipe id"),
 *                 @OA\Property(property="is_user_recepie", type="boolean", example=true),
 *                 @OA\Property(property="user_recepie_id", type="integer", example=3, description="Approved user recipe id"),
 *                 @OA\Property(property="meal_type", type="string", enum={"breakfast", "lunch", "dinner", "snack"}, description="Meal screen to write into"),
 *                 @OA\Property(property="amount", type="integer", example=100, description="Grams. Optional for a user recipe")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Recipe added successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Recipe added to meal successfully"),
 *             @OA\Property(property="user_meal_id", type="integer", example=50),
 *             @OA\Property(property="diary_note_id", type="integer", example=33)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Diary note not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Diary note not found")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="recepie_id is required")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Put(
 *     path="/api/diary/meal/recepie/update",
 *     summary="Update recipe amount in meal",
 *     description="Updates the amount of a recipe in a meal",
 *     operationId="updateRecepieInMeal",
 *     tags={"Diary - Recipes"},
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
 *                 required={"user_meal_id", "amount"},
 *                 @OA\Property(property="user_meal_id", type="integer", example=45, description="**REQUIRED**. User meal ID"),
 *                 @OA\Property(property="amount", type="integer", example=150, description="**REQUIRED**. New amount in grams")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Recipe amount updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Recipe amount updated successfully"),
 *             @OA\Property(property="user_meal_id", type="integer", example=49),
 *             @OA\Property(property="diary_note_id", type="integer", example=33)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Diary note not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Diary note not found")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="user_meal_id is required")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 *
 * @OA\Post(
 *     path="/api/diary/meal/recepie/create",
 *     summary="Create a user recipe",
 *     description="Отправить свой рецепт на модерацию. multipart/form-data: name, portions, ingredients (JSON-строка из product_id и grams), steps (JSON-строка шагов), photo (файл, необязательно), meal_types, components, cooking_methods, diets (JSON-массивы значений фильтра). КБЖУ считает сервер. Категории модератор может поправить. Пока он не подтвердит, рецепта нет в списке.",
 *     operationId="createUserRecepie",
 *     tags={"Diary - Recipes"},
 *     security={{"userSanctumToken": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"name", "ingredients"},
 *                 @OA\Property(property="name", type="string", example="Тыквенный суп"),
 *                 @OA\Property(property="portions", type="integer", example=2),
 *                 @OA\Property(property="ingredients", type="string", description="JSON array of product_id and grams"),
 *                 @OA\Property(property="steps", type="string", description="JSON array of cooking steps"),
 *                 @OA\Property(property="meal_types", type="string", description="JSON array: breakfast, lunch, dinner, snack"),
 *                 @OA\Property(property="components", type="string", description="JSON array: poultry, meat, fish, vegetables, fruits, sweet"),
 *                 @OA\Property(property="cooking_methods", type="string", description="JSON array: boiled, steamed, fried, stew, baked, basic"),
 *                 @OA\Property(property="diets", type="string", description="JSON array: vegetarian, vegan, low_fat, lots_of_fiber, low_carb, keto_diet, high_protein, lactose_free"),
 *                 @OA\Property(property="photo", type="string", format="binary", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Recipe submitted for moderation",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Recipe submitted for moderation"),
 *             @OA\Property(property="recepie", ref="#/components/schemas/UserRecepie")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 */

class RecepieController extends Controller
{
    //
}
