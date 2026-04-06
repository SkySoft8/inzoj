<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Restaurant Menu",
 *     description="Restaurant menu management endpoints (Restaurant User)"
 * )
 * 
 * @OA\Get(
 *     path="/api/restaurant/menu",
 *     summary="Get restaurant menu",
 *     description="Returns all dishes for authenticated restaurant",
 *     operationId="getRestaurantUserMenu",
 *     tags={"Restaurant Menu"},
 *     security={{"restaurantSanctumToken": {}}},
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Menu retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="restaurant",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Maslow6")
 *             ),
 *             @OA\Property(
 *                 property="dishes",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Dish")
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
 *         description="Restaurant not found"
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/api/restaurant/menu/dish",
 *     summary="Get dish details",
 *     description="Returns detailed information about a specific dish",
 *     operationId="getRestaurantUserDish",
 *     tags={"Restaurant Menu"},
 *     security={{"restaurantSanctumToken": {}}},
 *     
 *     @OA\Parameter(
 *         name="dish_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=1),
 *         description="Dish ID"
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
 *                 type="object",
 *                 @OA\Property(property="calories", type="number", format="float"),
 *                 @OA\Property(property="proteins", type="number", format="float"),
 *                 @OA\Property(property="fats", type="number", format="float"),
 *                 @OA\Property(property="carbs", type="number", format="float")
 *             ),
 *             @OA\Property(property="ratio", type="number", format="float")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Dish not found"
 *     )
 * )
 * 
 * @OA\Post(
 *     path="/api/restaurant/menu/dish/add",
 *     summary="Add new dish",
 *     description="Add a new dish to restaurant menu",
 *     operationId="addDish",
 *     tags={"Restaurant Menu"},
 *     security={{"restaurantSanctumToken": {}}},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"name", "price", "weight"},
 *                 @OA\Property(property="name", type="string", maxLength=255, example="Pizza Margherita"),
 *                 @OA\Property(property="price", type="integer", example=13),
 *                 @OA\Property(property="weight", type="integer", example=350),
 *                 @OA\Property(property="ingredients", type="string", example="Tomato sauce, mozzarella, basil"),
 *                 @OA\Property(property="proteins", type="number", format="float", example=12.5, nullable=true),
 *                 @OA\Property(property="fats", type="number", format="float", example=8.2, nullable=true),
 *                 @OA\Property(property="carbs", type="number", format="float", example=35.0, nullable=true),
 *                 @OA\Property(property="calories", type="number", format="float", example=250.0, nullable=true)
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=201,
 *         description="Dish added successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Dish added successfully"),
 *             @OA\Property(property="dish", ref="#/components/schemas/Dish")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 * 
 * @OA\Put(
 *     path="/api/restaurant/menu/dish/update",
 *     summary="Update dish",
 *     description="Update existing dish",
 *     operationId="updateDish",
 *     tags={"Restaurant Menu"},
 *     security={{"restaurantSanctumToken": {}}},
 *     
 *     @OA\Parameter(
 *         name="dish_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=1),
 *         description="Dish ID"
 *     ),
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"name", "price", "weight"},
 *                 @OA\Property(property="name", type="string", example="Pizza Margherita"),
 *                 @OA\Property(property="price", type="number", format="float", example=17),
 *                 @OA\Property(property="weight", type="number", format="float", example=400),
 *                 @OA\Property(property="ingredients", type="string"),
 *                 @OA\Property(property="proteins", type="number", format="float"),
 *                 @OA\Property(property="fats", type="number", format="float"),
 *                 @OA\Property(property="carbs", type="number", format="float"),
 *                 @OA\Property(property="calories", type="number", format="float")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Dish updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Dish updated successfully"),
 *             @OA\Property(property="dish", ref="#/components/schemas/Dish")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Dish not found"
 *     )
 * )
 * 
 * @OA\Delete(
 *     path="/api/restaurant/menu/dish/delete",
 *     summary="Delete dish",
 *     description="Delete dish from restaurant menu",
 *     operationId="deleteDish",
 *     tags={"Restaurant Menu"},
 *     security={{"restaurantSanctumToken": {}}},
 *     
 *     @OA\Parameter(
 *         name="dish_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=1),
 *         description="Dish ID"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Dish deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Dish deleted successfully")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Dish not found"
 *     )
 * )
 * 
 */

class RestaurantMenuController extends Controller
{
    //
}
