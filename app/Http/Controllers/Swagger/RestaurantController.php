<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Restaurants",
 *     description="Restaurant management"
 * )
 * 
 * @OA\Get(
 *     path="/api/restaurants",
 *     summary="Get restaurants list",
 *     description="Returns list of restaurants with optional type filter",
 *     operationId="getRestaurants",
 *     tags={"Restaurants"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\Parameter(
 *         name="restaurant_type",
 *         in="query",
 *         required=false,
 *         @OA\Schema(
 *             type="string",
 *             enum={"cafe", "canteen", "fine_restaurant", "fast_food", "bistro", "pub", "bar", "other"}
 *         ),
 *         description="Filter by restaurant type"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Restaurants retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="restaurants",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Restaurant")
 *             ),
 *             @OA\Property(
 *                 property="filters",
 *                 type="object",
 *                 @OA\Property(property="restaurant_type", type="string", nullable=true)
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 * 
 * @OA\Get(
 *     path="/api/restaurants/menu",
 *     summary="Get restaurant menu",
 *     description="Returns restaurant details and its dishes",
 *     operationId="getRestaurantMenu",
 *     tags={"Restaurants"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\Parameter(
 *         name="restaurant_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer", example=1),
 *         description="Restaurant ID"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Menu retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="restaurant", ref="#/components/schemas/Restaurant"),
 *             @OA\Property(
 *                 property="dishes",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Dish")
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=404,
 *         description="Restaurant not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Restaurant not found")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Missing required parameter",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="restaurant_id is required")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * ) 
 */

class RestaurantController extends Controller
{
    //
}
