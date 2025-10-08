<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Base\ApiController;
use App\Models\Artist;
use Illuminate\Http\JsonResponse;

class UserController extends ApiController
{
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

    public function logout(): JsonResponse
    {
        $this->user()->currentAccessToken()->delete();

        return $this->response->success();
    }
}
