<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login with NIP and password.
     * Returns access token using Passport.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'nip' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('nip', $request->nip)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'nip' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Generate Passport token
        $token = $user->createToken(config('app.name', 'Laravel') . ' Token')->accessToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->only(['id', 'name', 'email']),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'nip' => 'required|string|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nip' => $request->nip,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken(config('app.name', 'Laravel') . ' Token')->accessToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user->only(['id', 'name', 'email']),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Get authenticated user.
     */
    public function me(): JsonResponse
    {
        return response()->json([
            'user' => auth('api')->user(),
        ]);
    }

    /**
     * Logout - revoke the token.
     */
    public function logout(Request $request): JsonResponse
    {
        auth('api')->user()?->token()?->revoke();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Refresh the access token.
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Revoke old token
        $user->token()->revoke();

        // Create new token
        $newToken = $user->createToken(config('app.name', 'Laravel') . ' Token')->accessToken;

        return response()->json([
            'access_token' => $newToken,
            'token_type' => 'Bearer',
        ]);
    }
}
