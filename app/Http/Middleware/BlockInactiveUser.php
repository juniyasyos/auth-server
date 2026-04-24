<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BlockInactiveUser
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || $user->status === 'active') {
            return $next($request);
        }

        if ($request->routeIs('account.status') || $request->routeIs('logout')) {
            return $next($request);
        }

        $message = match ($user->status) {
            'inactive' => 'Akun Anda sedang dinonaktifkan oleh administrator.',
            'suspended' => 'Akun Anda telah ditangguhkan oleh administrator.',
            default => 'Akun Anda tidak dapat mengakses sistem saat ini. Mohon hubungi administrator.',
        };

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        return redirect()
            ->route('account.status')
            ->with('inactive_reason', $message);
    }
}
