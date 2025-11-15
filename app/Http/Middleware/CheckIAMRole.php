<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk validasi role berdasarkan IAM token.
 * Gunakan di route: ->middleware('iam.role:admin,manager')
 */
class CheckIAMRole
{
    /**
     * Handle an incoming request.
     *
     * @param  string|array  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $userRoles = $request->get('iam_user_roles', []);

        if (empty($userRoles)) {
            return response()->json([
                'error' => 'forbidden',
                'message' => 'No roles found in token',
            ], 403);
        }

        // Check if user has any of the required roles
        $hasRole = false;
        foreach ($roles as $role) {
            if (in_array($role, $userRoles)) {
                $hasRole = true;
                break;
            }
        }

        if (! $hasRole) {
            return response()->json([
                'error' => 'forbidden',
                'message' => 'Insufficient role. Required: '.implode(' or ', $roles),
            ], 403);
        }

        return $next($request);
    }
}
