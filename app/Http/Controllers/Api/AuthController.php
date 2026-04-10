<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * POST /api/auth/login
     * 
     * {
     *     "username": {username},
     *     "password": {password}
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        $token = JWTAuth::attempt($credentials);

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => auth()->user()->id,
                    'username' => auth()->user()->username,
                    'role' => auth()->user()->role,
                ],
            ],
        ]);
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * GET /api/auth/me
     */
    public function profile(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'created_at' => $user->created_at->toIso8601String(),
            ],
        ]);
    }
}
