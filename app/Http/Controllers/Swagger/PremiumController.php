<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Premium",
 *     description="Premium subscription management"
 * )
 * 
 * @OA\Post(
 *     path="/api/premium/purchase",
 *     summary="Purchase premium subscription",
 *     description="Activates premium subscription for the user",
 *     operationId="purchasePremium",
 *     tags={"Premium"},
 *     security={{"userSanctumToken": {}}},
 *     
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"plan"},
 *                 @OA\Property(
 *                     property="plan", 
 *                     type="string", 
 *                     enum={"quarterly", "yearly"}, 
 *                     description="Subscription plan",
 *                     example="yearly"
 *                 )
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Premium subscription activated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Premium subscription activated successfully"),
 *             @OA\Property(property="plan", type="string", enum={"quarterly", "yearly"}, example="yearly"),
 *             @OA\Property(property="premium_until", type="string", format="date-time", example="2026-04-04T00:00:00.000000Z")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=400,
 *         description="Premium already active",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Premium subscription is already active"),
 *             @OA\Property(property="premium_until", type="string", format="date-time", example="2026-04-04T00:00:00.000000Z")
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
 *                     property="plan",
 *                     type="array",
 *                     @OA\Items(type="string", example="The selected plan is invalid.")
 *                 )
 *             )
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=500,
 *         description="Payment processing failed",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Payment processing failed")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 */

class PremiumController extends Controller
{
    //
}
