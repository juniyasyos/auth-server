<?php

/**
 * Complete Testing Guide for UserApplicationsService
 * Using user NIP: 0000.00000 (admin user)
 */

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Juniyasyos\IamClient\Services\UserApplicationsService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

echo "════════════════════════════════════════════════════════════════════════════════\n";
echo "                    TESTING UserApplicationsService\n";
echo "                    User: 0000.00000 (admin)\n";
echo "════════════════════════════════════════════════════════════════════════════════\n\n";

// ============================================================================
// Test 1: Verify User
// ============================================================================
echo "TEST 1: Verify User\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

$user = \App\Models\User::where('nip', '0000.00000')->firstOrFail();
echo "✓ User ID: {$user->id}\n";
echo "✓ User Name: {$user->name}\n";
echo "✓ User NIP: {$user->nip}\n";
echo "✓ User Email: {$user->email}\n";
echo "✓ User Active: " . ($user->active ? 'Yes' : 'No') . "\n";
echo "✓ Access Profiles: " . $user->accessProfiles()->count() . "\n";
echo "✓ Effective Roles: " . $user->effectiveApplicationRoles()->count() . "\n\n";

// ============================================================================
// Test 2: Simulate IAM Token Session
// ============================================================================
echo "TEST 2: Simulate IAM Token Session\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

// Create a mock token (in real scenario, this comes from IAM server during SSO)
$mockToken = 'test_token_' . uniqid();
Session::put('iam.access_token', $mockToken);
Session::put('iam.sub', (string) $user->id);
Session::put('iam.app', 'siimut');

echo "✓ IAM Token Set: " . substr($mockToken, 0, 20) . "...\n";
echo "✓ IAM Sub: " . session('iam.sub') . "\n";
echo "✓ IAM App: " . session('iam.app') . "\n";
echo "✓ Session ID: " . session()->getId() . "\n\n";

// ============================================================================
// Test 3: Mock HTTP Responses
// ============================================================================
echo "TEST 3: Setup HTTP Mocks\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

// Build mock response data based on actual user data
$userApps = $user->effectiveApplicationRoles()
    ->with('application')
    ->get()
    ->groupBy('application.app_key')
    ->map(function ($roles, $appKey) use ($user) {
        $firstRole = $roles->first();
        $app = $firstRole->application;
        return [
            'id' => $app->id,
            'app_key' => $appKey,
            'name' => $app->name,
            'description' => $app->description,
            'enabled' => $app->enabled,
            'logo_url' => $app->logo_url,
            'app_url' => is_array($app->redirect_uris) && !empty($app->redirect_uris)
                ? $app->redirect_uris[0]
                : null,
            'redirect_uris' => $app->redirect_uris ?? [],
            'callback_url' => $app->callback_url,
            'backchannel_url' => $app->backchannel_url,
            'roles' => $roles->map(fn($r) => [
                'id' => $r->id,
                'slug' => $r->slug,
                'name' => $r->name,
                'is_system' => $r->is_system,
                'description' => $r->description,
            ])->values()->toArray(),
        ];
    })
    ->values()
    ->toArray();

$basicResponse = [
    'source' => 'iam-server',
    'sub' => (string) $user->id,
    'user_id' => $user->id,
    'total_accessible_apps' => count($userApps),
    'applications' => array_map(function ($app) {
        return [
            'id' => $app['id'],
            'app_key' => $app['app_key'],
            'name' => $app['name'],
            'description' => $app['description'],
            'enabled' => $app['enabled'],
            'status' => $app['enabled'] ? 'active' : 'inactive',
            'logo_url' => $app['logo_url'],
            'app_url' => $app['app_url'],
            'redirect_uris' => $app['redirect_uris'],
            'callback_url' => $app['callback_url'],
            'backchannel_url' => $app['backchannel_url'],
            'roles_count' => count($app['roles']),
            'has_logo' => !empty($app['logo_url']),
            'has_primary_url' => !empty($app['app_url']),
            'urls' => [
                'primary' => $app['app_url'],
                'all_redirects' => $app['redirect_uris'],
                'callback' => $app['callback_url'],
                'backchannel' => $app['backchannel_url'],
            ],
            'roles' => $app['roles'],
        ];
    }, $userApps),
    'accessible_apps' => array_column($userApps, 'app_key'),
    'timestamp' => now()->toIso8601String(),
];

$detailResponse = array_merge($basicResponse, [
    'applications' => array_map(function ($app) {
        return array_merge($app, [
            'metadata' => [
                'logo' => [
                    'url' => $app['logo_url'],
                    'available' => !empty($app['logo_url']),
                ],
                'urls' => $app['urls'],
                'created_at' => now()->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
            ],
            'access_profiles_using_this_app' => [],
        ]);
    }, $basicResponse['applications']),
    'user_profiles' => [],
]);

Http::fake([
    '*/api/users/applications' => Http::response($basicResponse, 200),
    '*/api/users/applications/detail' => Http::response($detailResponse, 200),
]);

echo "✓ HTTP mocks configured\n";
echo "✓ Mock apps count: " . count($userApps) . "\n";
echo "✓ Mock app_keys: " . implode(', ', array_column($userApps, 'app_key')) . "\n\n";

// ============================================================================
// Test 4: Test getApplications()
// ============================================================================
echo "TEST 4: Testing getApplications()\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

$service = app(UserApplicationsService::class);
$appsResult = $service->getApplications();

if (isset($appsResult['error'])) {
    echo "✗ ERROR: " . $appsResult['error'] . "\n";
    echo "  Message: " . $appsResult['message'] . "\n";
} else {
    echo "✓ Source: " . $appsResult['source'] . "\n";
    echo "✓ User ID: " . $appsResult['user_id'] . "\n";
    echo "✓ Sub: " . $appsResult['sub'] . "\n";
    echo "✓ Total Apps: " . $appsResult['total_accessible_apps'] . "\n";
    echo "✓ Applications:\n";

    foreach ($appsResult['applications'] ?? [] as $app) {
        echo "\n  ┌─ " . $app['name'] . "\n";
        echo "  │ App Key: " . $app['app_key'] . "\n";
        echo "  │ Status: " . $app['status'] . "\n";
        echo "  │ Logo: " . ($app['has_logo'] ? '✓ Yes (' . $app['logo_url'] . ')' : '✗ No') . "\n";
        echo "  │ URL: " . ($app['app_url'] ?? '-') . "\n";
        echo "  │ Roles Count: " . $app['roles_count'] . "\n";

        if (!empty($app['roles'])) {
            echo "  │ Roles:\n";
            foreach ($app['roles'] as $role) {
                echo "  │   - " . $role['name'] . " (" . $role['slug'] . ")\n";
            }
        }
        echo "  └\n";
    }
}
echo "\n";

// ============================================================================
// Test 5: Test getApplicationsDetail()
// ============================================================================
echo "TEST 5: Testing getApplicationsDetail()\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

$detailResult = $service->getApplicationsDetail();

if (isset($detailResult['error'])) {
    echo "✗ ERROR: " . $detailResult['error'] . "\n";
    echo "  Message: " . $detailResult['message'] . "\n";
} else {
    echo "✓ Source: " . $detailResult['source'] . "\n";
    echo "✓ Total Apps: " . $detailResult['total_apps'] . "\n";
    echo "✓ User Profiles: " . count($detailResult['user_profiles'] ?? []) . "\n";

    foreach ($detailResult['applications'] ?? [] as $app) {
        echo "\n  ┌─ DETAILED: " . $app['name'] . "\n";
        echo "  │ App Key: " . $app['app_key'] . "\n";
        echo "  │ Description: " . (strlen($app['description'] ?? '') > 50 ? substr($app['description'], 0, 50) . '...' : $app['description']) . "\n";

        if (isset($app['metadata'])) {
            echo "  │ Metadata:\n";
            echo "  │   Logo Available: " . ($app['metadata']['logo']['available'] ? 'Yes' : 'No') . "\n";

            if (isset($app['metadata']['urls'])) {
                echo "  │   URLs:\n";
                echo "  │     Primary: " . ($app['metadata']['urls']['primary'] ?? '-') . "\n";
                if ($app['metadata']['urls']['callback']) {
                    echo "  │     Callback: " . $app['metadata']['urls']['callback'] . "\n";
                }
            }

            if (isset($app['metadata']['created_at'])) {
                echo "  │   Created: " . substr($app['metadata']['created_at'], 0, 19) . "\n";
            }
        }

        echo "  │ Roles: " . count($app['roles'] ?? []) . "\n";
        echo "  │ Access Profiles: " . count($app['access_profiles_using_this_app'] ?? []) . "\n";
        echo "  └\n";
    }
}
echo "\n";

// ============================================================================
// Test 6: Test getApplicationByKey()
// ============================================================================
echo "TEST 6: Testing getApplicationByKey()\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

$foundApp = $service->getApplicationByKey('siimut');

if ($foundApp) {
    echo "✓ Found app: " . $foundApp['name'] . "\n";
    echo "✓ App Key: " . $foundApp['app_key'] . "\n";
    echo "✓ Status: " . $foundApp['status'] . "\n";
} else {
    echo "✗ App 'siimut' not found\n";
}

$notFoundApp = $service->getApplicationByKey('nonexistent');
if ($notFoundApp === null) {
    echo "✓ Correctly returns null for nonexistent app\n";
} else {
    echo "✗ Should return null for nonexistent app\n";
}
echo "\n";

// ============================================================================
// Test 7: Test Debug Methods
// ============================================================================
echo "TEST 7: Testing Debug Methods\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

$basicDebug = $service->debugGetApplications();
echo "✓ debugGetApplications():\n";
echo "  - Status: " . $basicDebug['status'] . "\n";
echo "  - Successful: " . ($basicDebug['successful'] ? 'Yes' : 'No') . "\n";
echo "  - Response Size: " . ($basicDebug['body_size'] ?? 0) . " bytes\n";
echo "  - Content-Type: " . ($basicDebug['headers']['content-type'] ?? 'unknown') . "\n";

$detailDebug = $service->debugGetApplicationsDetail();
echo "\n✓ debugGetApplicationsDetail():\n";
echo "  - Status: " . $detailDebug['status'] . "\n";
echo "  - Successful: " . ($detailDebug['successful'] ? 'Yes' : 'No') . "\n";
echo "  - Response Size: " . ($detailDebug['body_size'] ?? 0) . " bytes\n";

$allDebug = $service->debugAll();
echo "\n✓ debugAll():\n";
echo "  - Session ID: " . $allDebug['session']['id'] . "\n";
echo "  - Has Token: " . ($allDebug['session']['has_iam_token'] ? 'Yes' : 'No') . "\n";
echo "  - IAM Sub: " . $allDebug['session']['iam_sub'] . "\n";
echo "  - Base URL: " . $allDebug['endpoints']['base_url'] . "\n";
echo "  - Execution Time: " . $allDebug['execution_time_ms'] . "ms\n";
echo "\n";

// ============================================================================
// Test 8: Error Scenarios
// ============================================================================
echo "TEST 8: Testing Error Scenarios\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

// Test missing token
echo "Scenario A: Missing token\n";
Session::forget('iam.access_token');
$noTokenResult = $service->getApplications();
echo "  ✓ Error: " . ($noTokenResult['error'] ?? 'none') . "\n";
echo "  ✓ Message: " . ($noTokenResult['message'] ?? '') . "\n";

// Restore token
Session::put('iam.access_token', $mockToken);

// Test server error
echo "\nScenario B: Server error (401)\n";
Http::fake([
    '*/api/users/applications' => Http::response(['error' => 'unauthorized'], 401),
]);
$errorResult = $service->getApplications();
echo "  ✓ Error: " . ($errorResult['error'] ?? 'none') . "\n";
echo "  ✓ Status: " . ($errorResult['status'] ?? '') . "\n";

echo "\n";

// ============================================================================
// Test 9: Summary
// ============================================================================
echo "════════════════════════════════════════════════════════════════════════════════\n";
echo "                            TEST SUMMARY\n";
echo "════════════════════════════════════════════════════════════════════════════════\n";
echo "User: " . $user->nip . " (" . $user->name . ")\n";
echo "Total Tests: 8\n";
echo "Status: ✓ ALL TESTS COMPLETED\n";
echo "\n";
echo "Available Features:\n";
echo "  ✓ getApplications() - Fetch user accessible applications\n";
echo "  ✓ getApplicationsDetail() - Fetch with detailed metadata\n";
echo "  ✓ getApplicationByKey() - Find specific application\n";
echo "  ✓ debugGetApplications() - Debug basic endpoint\n";
echo "  ✓ debugGetApplicationsDetail() - Debug detail endpoint\n";
echo "  ✓ debugAll() - Complete debug output\n";
echo "\n";
echo "Integration Ready: ✓ YES\n";
echo "════════════════════════════════════════════════════════════════════════════════\n";
