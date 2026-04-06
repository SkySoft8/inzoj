<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Diary - Meal",
 *     description="Meals management endpoints (products and recipes)"
 * )
 * 
 * @OA\Get(
 *     path="/api/diary/meal",
 *     summary="Get meal list (products or recipes)",
 *     description="Returns list of products or recipes for meal selection",
 *     operationId="getMeals",
 *     tags={"Diary - Meal"},
 *     security={{"userSanctumToken": {}}},
 * 
 *     @OA\Parameter(
 *         name="meal_type",
 *         in="query",
 *         required=true,
 *         @OA\Schema(
 *             type="string",
 *             enum={"breakfast", "lunch", "dinner", "snack"},
 *             default="lunch"
 *         ),
 *         description="Type of meal"
 *     ),
 *     
 *     @OA\Parameter(
 *         name="productsOrRecepies",
 *         in="query",
 *         required=true,
 *         @OA\Schema(
 *             type="string",
 *             enum={"products", "recepies"},
 *             default="products"
 *         ),
 *         description="Type of items to return (products or recipes)"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Meals retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="products",
 *                 type="array",
 *                 nullable=true,
 *                 @OA\Items(ref="#/components/schemas/Product")
 *             ),
 *             @OA\Property(
 *                 property="recepies",
 *                 type="array",
 *                 nullable=true,
 *                 @OA\Items(ref="#/components/schemas/Recepie")
 *             ),
 *             @OA\Property(
 *                 property="meal_type",
 *                 type="string",
 *                 enum={"breakfast", "lunch", "dinner", "snack"},
 *                 nullable=true
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
 * @OA\Post(
 *     path="/api/diary/meal/toggle-favorite",
 *     summary="Toggle favorite (product or recipe)",
 *     description="Add or remove product/recipe from user's favorites",
 *     operationId="toggleFavorite",
 *     tags={"Diary - Meal"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"productsOrRecepies", "is_favorite"},
 *                 @OA\Property(
 *                     property="productsOrRecepies",
 *                     type="string",
 *                     enum={"products", "recepies"},
 *                     description="Type of item"
 *                 ),
 *                 @OA\Property(
 *                     property="product_id",
 *                     type="integer",
 *                     example=2,
 *                     description="Required if productsOrRecepies=products"
 *                 ),
 *                 @OA\Property(
 *                     property="recepie_id",
 *                     type="integer",
 *                     example=1,
 *                     description="Required if productsOrRecepies=recepies"
 *                 ),
 *                 @OA\Property(
 *                     property="is_favorite",
 *                     type="boolean",
 *                     example=false,
 *                     description="Current favorite status (false = add, true = remove)"
 *                 )
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Favorite toggled successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="is_favorite", type="boolean")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Get(
 *     path="/api/diary/meal/filter",
 *     summary="Get available filters",
 *     description="Returns all available filter options for recipes",
 *     operationId="getFilters",
 *     tags={"Diary - Meal"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Filters retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="filters",
 *                 type="object",
 *                 @OA\Property(
 *                     property="meal_types",
 *                     type="array",
 *                     @OA\Items(type="string")
 *                 ),
 *                 @OA\Property(
 *                     property="components",
 *                     type="array",
 *                     @OA\Items(type="string")
 *                 ),
 *                 @OA\Property(
 *                     property="cooking_methods",
 *                     type="array",
 *                     @OA\Items(type="string")
 *                 ),
 *                 @OA\Property(
 *                     property="diets",
 *                     type="array",
 *                     @OA\Items(type="string")
 *                 )
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Get(
 *     path="/api/diary/meal/filter/apply",
 *     summary="Apply filters to recipes",
 *     description="Returns filtered recipes based on selected filters",
 *     operationId="applyFilters",
 *     tags={"Diary - Meal"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\Parameter(
 *         name="meal_type",
 *         in="query",
 *         required=false,
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(
 *                 type="string",
 *                 enum={"breakfast", "lunch", "dinner", "snack"}
 *             )
 *         ),
 *         description="Filter by meal type (multiple allowed)"
 *     ),
 *     
 *     @OA\Parameter(
 *         name="component",
 *         in="query",
 *         required=false,
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(
 *                 type="string",
 *                 enum={"poultry", "meat", "fish", "vegetables", "fruits", "sweet"}
 *             )
 *         ),
 *         description="Filter by ingredients/components (multiple allowed)"
 *     ),
 *     
 *     @OA\Parameter(
 *         name="cooking_method",
 *         in="query",
 *         required=false,
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(
 *                 type="string",
 *                 enum={"boiled", "steamed", "fried", "stew", "baked", "basic"}
 *             )
 *         ),
 *         description="Filter by cooking method (multiple allowed). **Example:** boiled"
 *     ),
 *     
 *     @OA\Parameter(
 *         name="diet",
 *         in="query",
 *         required=false,
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(
 *                 type="string",
 *                 enum={"vegetarian", "vegan", "low_fat", "lots_of_fiber", "low_carb", "keto_diet", "high_protein", "lactose_free"}
 *             )
 *         ),
 *         description="Filter by diet type (multiple allowed)"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Filtered recipes retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="products",
 *                 type="array",
 *                 nullable=true,
 *                 @OA\Items(ref="#/components/schemas/Product")
 *             ),
 *             @OA\Property(
 *                 property="recepies",
 *                 type="array",
 *                 nullable=true,
 *                 @OA\Items(ref="#/components/schemas/Recepie")
 *             ),
 *             @OA\Property(
 *                 property="meal_type",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 nullable=true
 *             ),
 *             @OA\Property(
 *                 property="component",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 nullable=true
 *             ),
 *             @OA\Property(
 *                 property="cooking_method",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 nullable=true
 *             ),
 *             @OA\Property(
 *                 property="diet",
 *                 type="array",
 *                 @OA\Items(type="string"),
 *                 nullable=true
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 */

class MealController extends Controller
{
    //
}
