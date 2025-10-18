<?php

namespace App\Http\Controllers\Sso;

use App\Http\Controllers\Controller;
use App\Services\Sso\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class SsoVerifyController extends Controller
{
    public function __construct(private readonly TokenService $tokens)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
        ]);

        try {
            $payload = $this->tokens->verify($validated['token']);
        } catch (\Throwable $exception) {
            return response()->json([
                'message' => 'Invalid or expired token.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'user' => [
                'id' => $payload['sub'] ?? null,
                'email' => $payload['email'] ?? null,
            ],
            'app' => $payload['app'] ?? null,
            'issuer' => $payload['iss'] ?? null,
            'expires_at' => isset($payload['exp']) ? Carbon::createFromTimestamp($payload['exp'])->toIso8601String() : null,
        ]);
    }
}
