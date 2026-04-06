<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Diary - Water",
 *     description="Water consumption management in diary"
 * )
 * 
 * @OA\Put(
 *     path="/api/diary/water",
 *     summary="Update water counter",
 *     description="Increases or decreases water consumption by 0.25L",
 *     operationId="updateWaterCounter",
 *     tags={"Diary - Water"},
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
 *         name="type",
 *         in="query",
 *         required=true,
 *         @OA\Schema(
 *             type="string",
 *             enum={"empty", "full"}
 *         ),
 *         description="Action type: 'empty' - add water (+0.25L), 'full' - remove water (-0.25L)"
 *     ),
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Water counter updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Water counter increased successfully"),
 *             @OA\Property(property="current_water", type="number", format="float", example=1.50)
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=400,
 *         description="Diary note not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Diary note not found")
 *         )
 *     ),
 *     
 *     @OA\Response(
 *         response=422,
 *         description="Missing required parameter",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="diary_note_id is required")
 *         )
 *     ),
 *     
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 */

class WaterController extends Controller
{
    //
}
