<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Base\ApiController;
use App\Models\Artist;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="Operations related to users"
 * )
 */
class UserController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/user",
     *     summary="Get authenticated user data",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="item", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                     @OA\Property(property="favorite_artists", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=101),
     *                             @OA\Property(property="title", type="string", example="Artist Name"),
     *                             @OA\Property(property="genres", type="string", example="Pop, Rock"),
     *                             @OA\Property(property="logo", type="string", example="https://example.com/logo.png"),
     *                             @OA\Property(property="pivot", type="object",
     *                                 @OA\Property(property="last_checked_album_id", type="integer", example=1001),
     *                                 @OA\Property(property="listening_now", type="integer", enum={0, 1}, example=0)
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $user = $this->user();
        $user->load('favoriteArtists:' . implode(',', [
            Artist::ID,
            Artist::TITLE,
            Artist::GENRES,
        ]));

        return $this->response->item($user);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/login",
     *     summary="User login attempt",
     *     tags={"Users", "Authorization"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="1|abcdefghijklmnopqrstuvwxyz0123456789abcdef")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="description", type="string", example="Invalid credentials.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="validation.required"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="validation.required")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="validation.required"))
     *             )
     *         )
     *     )
     * )
     */
    public function login(): JsonResponse
    {
        $input = $this->request->validate([
            'email' => 'required|email:rfc|max:255',
            'password' => 'required|string|max:255',
        ]);

        if (!auth()->attempt($input)) {
            return $this->response->unauthorized('Invalid credentials.');
        }

        $user = $this->user();
        $token = $user->createToken('api-token')->plainTextToken;

        return $this->response->success(['token' => $token]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/logout",
     *     summary="User logout",
     *     tags={"Users", "Authorization"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function logout(): JsonResponse
    {
        $this->user()->currentAccessToken()->delete();

        return $this->response->success();
    }
}
