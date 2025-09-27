<?php

namespace App\Http\Controllers;

use App\Services\Contracts\AppRegistryContract;
use App\Services\Contracts\ClaimsBuilderContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserInfoController extends Controller
{
    public function __invoke(Request $request, ClaimsBuilderContract $claimsBuilder, AppRegistryContract $registry): JsonResponse
    {
        $user = $request->user();

        $application = null;
        if ($request->filled('app')) {
            $application = $registry->getByKeyOrFail($request->query('app'));
        }

        $claims = $claimsBuilder->build($user, $application);

        return response()->json([
            'sub' => (string) $user->getKey(),
            'name' => $user->name,
            'email' => $user->email,
            'claims' => $claims,
        ]);
    }
}
