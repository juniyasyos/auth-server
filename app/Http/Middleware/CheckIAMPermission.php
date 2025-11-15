<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk validasi permission berdasarkan IAM token.
 * Gunakan di route: ->middleware('iam.permission:create-post,edit-post')
 */
class CheckIAMPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  string|array  $permissions
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $userPermissions = $request->get('iam_user_permissions', []);

        if (empty($userPermissions)) {
            return response()->json([
                'error' => 'forbidden',
                'message' => 'No permissions found in token',
            ], 403);
        }

        // Check if user has any of the required permissions
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions)) {
                $hasPermission = true;
                break;
            }
        }

        if (! $hasPermission) {
            return response()->json([
                'error' => 'forbidden',
                'message' => 'Insufficient permissions. Required: '.implode(' or ', $permissions),
            ], 403);
        }

        return $next($request);
    }
}
