<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk inject IAM user context ke request.
 * Harus digunakan setelah VerifyIAMAccessToken.
 */
class InjectIAMUserContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Context sudah di-inject oleh VerifyIAMAccessToken
        // Middleware ini untuk extend functionality jika diperlukan

        $token = $request->attributes->get('iam_token');

        if ($token) {
            // Bisa tambahkan logging atau audit trail
            Log::info('IAM User Access', [
                'user_id' => $token->sub,
                'user_email' => $token->email ?? null,
                'roles' => $token->roles ?? [],
                'endpoint' => $request->path(),
                'method' => $request->method(),
            ]);
        }

        return $next($request);
    }
}
