<?php

namespace App\Http\Controllers;

use App\Services\Contracts\AppRegistryContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserInfoController extends Controller
{
    public function __invoke(Request $request, AppRegistryContract $registry): JsonResponse
    {
        $user = $request->user();

        $application = null;
        if ($request->filled('app')) {
            $application = $registry->getByKeyOrFail($request->query('app'));
        }

        $claims = [
            'apps' => [],
            'roles' => [],
        ];

        if ($application) {
            $claims['application_id'] = $application->getKey();
            $claims['app_key'] = $application->app_key;
        }

        return response()->json([
            'sub' => (string) $user->getKey(),
            'name' => $user->name,
            'email' => $user->email,
            'claims' => $claims,
        ]);
    }
}
