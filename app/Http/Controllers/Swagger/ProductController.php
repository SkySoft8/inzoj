<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Diary - Products",
 *     description="Product management in diary meals"
 * )
 * 
 * @OA\Get(
 *     path="/api/diary/meal/product/search",
 *     summary="Search products for a custom recipe",
 *     description="Name search. calories, proteins, fats and carbs are per 100g. serving_label and serving_grams are the optional portion caption.",
 *     operationId="searchProducts",
 *     tags={"Diary - Products"},
 *     security={{"userSanctumToken": {}}},
 *     @OA\Parameter(name="q", in="query", @OA\Schema(type="string", example="тыква")),
 *     @OA\Response(response=200, description="Matching products"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 *
 * @OA\Get(
 *     path="/api/diary/meal/product",
 *     summary="Get product details",
 *     description="Returns product information for editing or adding to meal",
 *     operationId="getProduct",
 *     tags={"Diary - Products"},
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
 *         name="product_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=2),
 *         description="Product ID"
 *     ),
 *     
 *     @OA\Parameter(
 *         name="meal_type",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", enum={"breakfast", "lunch", "dinner", "snack"}),
 *         description="Type of meal (required when adding new product)"
 *     ),
 * 
 *     @OA\Parameter(
 *         name="user_meal_id",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="integer", example=49),
 *         description="User meal ID (for editing existing meal)"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Product retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="product", ref="#/components/schemas/Product"),
 *             @OA\Property(property="amount", type="integer", example=120, description="Grams used for nutrition"),
 *             @OA\Property(property="portion_quantity", type="integer", nullable=true, example=1),
 *             @OA\Property(property="portion_unit", type="string", nullable=true, enum={"g", "pcs", "ml"}, example="pcs"),
 *             @OA\Property(property="portion_label", type="string", nullable=true, example="1 шт"),
 *             @OA\Property(property="diary_note_id", type="integer", example=33),
 *             @OA\Property(property="user_meal_id", type="integer", nullable=true)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Product not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Product not found")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Post(
 *     path="/api/diary/meal/product/add",
 *     summary="Add product to meal",
 *     description="Adds a product to the specified meal",
 *     operationId="addProductToMeal",
 *     tags={"Diary - Products"},
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
 *                 required={"product_id", "meal_type", "quantity"},
 *                 @OA\Property(property="product_id", type="integer", example=3),
 *                 @OA\Property(property="meal_type", type="string", enum={"breakfast", "lunch", "dinner", "snack"}),
 *                 @OA\Property(property="quantity", type="integer", example=1, description="How many units"),
 *                 @OA\Property(property="unit", type="string", enum={"g", "pcs", "ml"}, example="pcs", description="g is always allowed. pcs and ml only if the product has that measure"),
 *                 @OA\Property(property="amount", type="integer", example=100, description="Legacy grams. Used when quantity is omitted")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Product added successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Product added to meal successfully"),
 *             @OA\Property(property="user_meal_id", type="integer", example=50),
 *             @OA\Property(property="diary_note_id", type="integer", example=33)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="diary_note_id is required")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Put(
 *     path="/api/diary/meal/product/update",
 *     summary="Update product amount in meal",
 *     description="Updates the amount of a product in a meal",
 *     operationId="updateProductInMeal",
 *     tags={"Diary - Products"},
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
 *                 required={"user_meal_id", "quantity"},
 *                 @OA\Property(property="user_meal_id", type="integer", example=49),
 *                 @OA\Property(property="quantity", type="integer", example=1),
 *                 @OA\Property(property="unit", type="string", enum={"g", "pcs", "ml"}, example="g"),
 *                 @OA\Property(property="amount", type="integer", example=150, description="Legacy grams")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Product amount updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Product amount updated successfully"),
 *             @OA\Property(property="user_meal_id", type="integer", example=49),
 *             @OA\Property(property="diary_note_id", type="integer", example=33)
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
 * 
 */

class ProductController extends Controller
{
    //
}
