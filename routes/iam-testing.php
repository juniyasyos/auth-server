<?php

use App\Domain\Iam\Services\RoleService;
use App\Domain\Iam\Services\TokenBuilder;
use App\Domain\Iam\Services\UserRoleAssignmentService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| IAM Testing Routes
|--------------------------------------------------------------------------
|
| Testing routes for the new IAM role-mapping system.
| These demonstrate token generation, role management, and user assignments.
|
*/

Route::prefix('iam-test')->group(function () {

    // Test 1: Generate token for a user
    Route::get('/token/{email}', function (string $email) {
        $user = \App\Models\User::where('email', $email)->firstOrFail();
        $tokenBuilder = app(TokenBuilder::class);

        $token = $tokenBuilder->buildTokenForUser($user);
        $claims = $tokenBuilder->decode($token);

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'unit' => $user->unit,
            ],
            'token' => $token,
            'claims' => [
                'sub' => $claims->userId,
                'email' => $claims->email,
                'name' => $claims->name,
                'apps' => $claims->apps,
                'roles_by_app' => $claims->rolesByApp,
                'unit' => $claims->unit,
                'iss' => $claims->issuer,
                'iat' => $claims->issuedAt,
                'exp' => $claims->expiresAt,
                'ttl' => $claims->getTimeUntilExpiry(),
            ],
            'verification' => $tokenBuilder->isValid($token),
        ]);
    })->name('iam-test.token');

    // Test 2: View user's roles and access
    Route::get('/user-access/{email}', function (string $email) {
        $user = \App\Models\User::where('email', $email)->firstOrFail();
        $assignmentService = app(UserRoleAssignmentService::class);

        $apps = $assignmentService->getAppsForUser($user);
        $rolesByApp = $assignmentService->getRolesByAppForUser($user);

        $detailedAccess = [];
        foreach ($rolesByApp as $appKey => $roles) {
            $app = \App\Domain\Iam\Models\Application::where('app_key', $appKey)->first();
            $detailedAccess[] = [
                'app_key' => $appKey,
                'app_name' => $app?->name ?? 'Unknown',
                'roles' => $roles,
                'role_count' => count($roles),
            ];
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'unit' => $user->unit,
            ],
            'summary' => [
                'total_apps' => count($apps),
                'total_role_assignments' => $user->applicationRoles()->count(),
            ],
            'accessible_apps' => $apps,
            'roles_by_app' => $rolesByApp,
            'detailed_access' => $detailedAccess,
        ]);
    })->name('iam-test.user-access');

    // Test 3: List roles for an application
    Route::get('/roles/{appKey}', function (string $appKey) {
        $roleService = app(RoleService::class);
        $roles = $roleService->getRolesForApplication($appKey);

        $application = \App\Domain\Iam\Models\Application::where('app_key', $appKey)->firstOrFail();

        return response()->json([
            'success' => true,
            'application' => [
                'app_key' => $application->app_key,
                'name' => $application->name,
                'enabled' => $application->enabled,
            ],
            'roles' => $roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'slug' => $role->slug,
                    'name' => $role->name,
                    'description' => $role->description,
                    'is_system' => $role->is_system,
                    'user_count' => $role->users()->count(),
                ];
            }),
            'summary' => [
                'total_roles' => $roles->count(),
                'system_roles' => $roles->where('is_system', true)->count(),
                'custom_roles' => $roles->where('is_system', false)->count(),
            ],
        ]);
    })->name('iam-test.roles');

    // Test 4: Test role assignment
    Route::post('/assign-role', function () {
        $validated = request()->validate([
            'user_email' => 'required|email|exists:users,email',
            'app_key' => 'required|exists:applications,app_key',
            'role_slug' => 'required|string',
        ]);

        $user = \App\Models\User::where('email', $validated['user_email'])->firstOrFail();
        $roleService = app(RoleService::class);
        $assignmentService = app(UserRoleAssignmentService::class);

        $role = $roleService->findRoleBySlug($validated['app_key'], $validated['role_slug']);
        $assignmentService->assignRoleToUser($user, $role);

        return response()->json([
            'success' => true,
            'message' => "Role '{$role->name}' assigned to {$user->name}",
            'user' => $user->email,
            'role' => $role->slug,
            'application' => $validated['app_key'],
        ]);
    })->name('iam-test.assign-role');

    // Test 5: Complete flow test
    Route::get('/complete-flow', function () {
        $tokenBuilder = app(TokenBuilder::class);
        $assignmentService = app(UserRoleAssignmentService::class);

        // Test with admin user
        $admin = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $adminToken = $tokenBuilder->buildTokenForUser($admin);
        $adminClaims = $tokenBuilder->decode($adminToken);

        // Test with doctor user
        $doctor = \App\Models\User::where('email', 'doctor@gmail.com')->first();
        $doctorToken = $tokenBuilder->buildTokenForUser($doctor);
        $doctorClaims = $tokenBuilder->decode($doctorToken);

        return response()->json([
            'success' => true,
            'message' => 'IAM system is fully operational',
            'tests' => [
                'admin_token' => [
                    'user' => $admin->email,
                    'apps_count' => count($adminClaims->apps),
                    'apps' => $adminClaims->apps,
                    'roles_count' => array_sum(array_map('count', $adminClaims->rolesByApp)),
                    'token_valid' => $tokenBuilder->isValid($adminToken),
                ],
                'doctor_token' => [
                    'user' => $doctor->email,
                    'apps_count' => count($doctorClaims->apps),
                    'apps' => $doctorClaims->apps,
                    'roles_count' => array_sum(array_map('count', $doctorClaims->rolesByApp)),
                    'token_valid' => $tokenBuilder->isValid($doctorToken),
                    'has_siimut_access' => $doctorClaims->hasAccessToApp('siimut'),
                    'has_pharmacy_access' => $doctorClaims->hasAccessToApp('pharmacy.app'),
                ],
            ],
            'statistics' => [
                'total_users' => \App\Models\User::count(),
                'total_applications' => \App\Domain\Iam\Models\Application::count(),
                'total_iam_roles' => \App\Domain\Iam\Models\Role::count(),
                'total_assignments' => \App\Domain\Iam\Models\UserApplicationRole::count(),
            ],
        ]);
    })->name('iam-test.complete-flow');

    // Test 6: Role-to-Permission mapping example (client-side simulation)
    Route::get('/permission-mapping/{email}/{appKey}', function (string $email, string $appKey) {
        $user = \App\Models\User::where('email', $email)->firstOrFail();
        $tokenBuilder = app(TokenBuilder::class);
        $assignmentService = app(UserRoleAssignmentService::class);

        $token = $tokenBuilder->buildTokenForUser($user);
        $claims = $tokenBuilder->decode($token);

        // Check if user has access to the app
        if (! $claims->hasAccessToApp($appKey)) {
            return response()->json([
                'success' => false,
                'message' => "User does not have access to {$appKey}",
            ], 403);
        }

        // Get user's roles in this app
        $userRoles = $claims->getRolesForApp($appKey);

        // Simulate client-side role-to-permission mapping
        $permissionMap = [
            'siimut' => [
                'admin' => ['*'], // All permissions
                'doctor' => ['patient.view', 'patient.create', 'patient.edit', 'report.view', 'report.create', 'prescription.create'],
                'nurse' => ['patient.view', 'patient.edit', 'report.view'],
                'receptionist' => ['patient.view', 'patient.create', 'appointment.create'],
                'viewer' => ['patient.view', 'report.view'],
            ],
            'pharmacy.app' => [
                'admin' => ['*'],
                'pharmacist' => ['inventory.view', 'inventory.edit', 'prescription.view', 'prescription.fulfill'],
                'assistant' => ['inventory.view', 'prescription.view'],
                'viewer' => ['inventory.view'],
            ],
        ];

        // Calculate user's permissions based on their roles
        $permissions = [];
        if (isset($permissionMap[$appKey])) {
            foreach ($userRoles as $roleSlug) {
                if (isset($permissionMap[$appKey][$roleSlug])) {
                    $permissions = array_merge($permissions, $permissionMap[$appKey][$roleSlug]);
                }
            }
        }
        $permissions = array_unique($permissions);

        return response()->json([
            'success' => true,
            'user' => $user->email,
            'application' => $appKey,
            'user_roles' => $userRoles,
            'calculated_permissions' => $permissions,
            'has_all_permissions' => in_array('*', $permissions),
            'example_checks' => [
                'patient.view' => in_array('*', $permissions) || in_array('patient.view', $permissions),
                'patient.create' => in_array('*', $permissions) || in_array('patient.create', $permissions),
                'patient.delete' => in_array('*', $permissions) || in_array('patient.delete', $permissions),
            ],
            'note' => 'This demonstrates how client applications map IAM roles to their own permissions',
        ]);
    })->name('iam-test.permission-mapping');

    // Test 7: Dashboard with all statistics
    Route::get('/dashboard', function () {
        $users = \App\Models\User::all();
        $applications = \App\Domain\Iam\Models\Application::all();
        $roles = \App\Domain\Iam\Models\Role::all();
        $assignments = \App\Domain\Iam\Models\UserApplicationRole::with('user', 'role.application')->get();

        return response()->json([
            'success' => true,
            'statistics' => [
                'users' => [
                    'total' => $users->count(),
                    'active' => $users->where('active', true)->count(),
                ],
                'applications' => [
                    'total' => $applications->count(),
                    'enabled' => $applications->where('enabled', true)->count(),
                ],
                'roles' => [
                    'total' => $roles->count(),
                    'system_roles' => $roles->where('is_system', true)->count(),
                    'custom_roles' => $roles->where('is_system', false)->count(),
                ],
                'assignments' => [
                    'total' => $assignments->count(),
                ],
            ],
            'users' => $users->map(function ($user) {
                return [
                    'email' => $user->email,
                    'name' => $user->name,
                    'unit' => $user->unit,
                    'apps_count' => $user->accessibleApps() ? count($user->accessibleApps()) : 0,
                    'roles_count' => $user->applicationRoles()->count(),
                ];
            }),
            'applications' => $applications->map(function ($app) {
                return [
                    'app_key' => $app->app_key,
                    'name' => $app->name,
                    'enabled' => $app->enabled,
                    'roles_count' => $app->roles()->count(),
                ];
            }),
            'recent_assignments' => $assignments->take(10)->map(function ($assignment) {
                return [
                    'user' => $assignment->user->email,
                    'role' => $assignment->role->slug,
                    'application' => $assignment->role->application->app_key,
                    'assigned_at' => $assignment->created_at->toDateTimeString(),
                ];
            }),
        ]);
    })->name('iam-test.dashboard');
});
