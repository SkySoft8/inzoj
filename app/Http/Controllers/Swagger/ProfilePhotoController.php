<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;

/**
 * @OA\Tag(
 *     name="Profile Photo",
 *     description="Private profile photo upload, replacement and delivery"
 * )
 *
 * @OA\Post(
 *     path="/api/profile/photo",
 *     summary="Upload or replace profile photo",
 *     description="Stores the uploaded image on the private disk and replaces the previous profile photo if one exists.",
 *     operationId="uploadProfilePhoto",
 *     tags={"Profile Photo"},
 *     security={{"userSanctumToken": {}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"photo"},
 *                 @OA\Property(property="photo", type="string", format="binary", description="Image up to 5 MB (jpeg, jpg, png, webp)")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Profile photo saved",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Profile photo updated"),
 *             @OA\Property(property="profile_photo_url", type="string", example="http://localhost:8000/api/profile/photo/file")
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 *
 * @OA\Delete(
 *     path="/api/profile/photo",
 *     summary="Delete profile photo",
 *     operationId="deleteProfilePhoto",
 *     tags={"Profile Photo"},
 *     security={{"userSanctumToken": {}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Profile photo deleted",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Profile photo deleted")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 *
 * @OA\Get(
 *     path="/api/profile/photo/file",
 *     summary="Get profile photo file",
 *     description="Streams the authenticated user's profile photo from the private disk.",
 *     operationId="getProfilePhotoFile",
 *     tags={"Profile Photo"},
 *     security={{"userSanctumToken": {}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Image content",
 *         @OA\MediaType(mediaType="image/*", @OA\Schema(type="string", format="binary"))
 *     ),
 *     @OA\Response(response=404, description="Profile photo not found"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 */
class ProfilePhotoController extends Controller
{
}
