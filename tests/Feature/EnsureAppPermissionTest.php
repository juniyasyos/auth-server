<?php

use App\Models\User;
use App\Services\Contracts\RbacServiceContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\PermissionRegistrar;
use Tests\Support\Middleware\InjectClaims;

uses(RefreshDatabase::class);

beforeEach(function () {
    Config::set('cache.default', 'array');
    Config::set('permission.cache.store', 'array');

    app('router')->aliasMiddleware('inject.claims', InjectClaims::class);
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

function registerSecureRoute(string $uri, array $middleware): void
{
    Route::middleware($middleware)
        ->get($uri, fn () => response()->json(['ok' => true]));
}

it('allows_request_when_claims_contain_permission', function () {
    makeIamApplication('siimut');
    $user = User::factory()->create();

    registerSecureRoute('/testing/claims-allowed', [
        'inject.claims:siimut.indicator.view',
        'ensure.app.permission:siimut,siimut.indicator.view',
    ]);

    $this->actingAs($user)
        ->getJson('/testing/claims-allowed')
        ->assertOk();
});

it('denies_request_when_claims_missing_permission', function () {
    makeIamApplication('siimut');
    $user = User::factory()->create();

    registerSecureRoute('/testing/claims-denied', [
        'inject.claims:',
        'ensure.app.permission:siimut,siimut.indicator.view',
    ]);

    $this->actingAs($user)
        ->getJson('/testing/claims-denied')
        ->assertForbidden();
});

it('falls_back_to_rbac_service_when_no_claims_present', function () {
    $application = makeIamApplication('siimut');
    $user = User::factory()->create();
    $role = makeIamRole($application, 'admin');
    $permission = makeIamPermission($application, 'indicator', 'view')->name;

    /** @var RbacServiceContract $rbac */
    $rbac = app(RbacServiceContract::class);
    $rbac->assignRole($user, $role->name, $application);
    $rbac->syncPermissions($role, [$permission], $application);

    registerSecureRoute('/testing/rbac-fallback', [
        'ensure.app.permission:siimut,siimut.indicator.view',
    ]);

    $this->actingAs($user)
        ->getJson('/testing/rbac-fallback')
        ->assertOk();
});

it('handles_nonexistent_application_key', function () {
    $user = User::factory()->create();

    registerSecureRoute('/testing/missing-app', [
        'ensure.app.permission:unknown,unknown.permission',
    ]);

    $this->actingAs($user)
        ->getJson('/testing/missing-app')
        ->assertNotFound();
});
