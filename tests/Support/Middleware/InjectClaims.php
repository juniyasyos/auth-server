<?php

namespace Tests\Support\Middleware;

use Closure;
use Illuminate\Http\Request;

class InjectClaims
{
    public function handle(Request $request, Closure $next, string $permissions = '')
    {
        $claims = $permissions === '' ? [] : array_filter(explode('|', $permissions));
        $request->attributes->set('rbac_claims', ['perms' => $claims]);

        return $next($request);
    }
}
