<?php

use App\Events\PermissionSynced;
use App\Events\RoleAssigned as AppRoleAssigned;
use App\Events\RoleRevoked as AppRoleRevoked;
use App\Listeners\InvalidateUserRbacCache;
use App\Models\User;
use App\Services\Contracts\CacheResolverContract;
use App\Services\Contracts\RbacServiceContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\PermissionRegistrar;
use Tests\Support\Concerns\SetsCurrentApplication;

uses(Tests\TestCase::class, RefreshDatabase::class, SetsCurrentApplication::class);

beforeEach(function () {
    Config::set('cache.default', 'array');
    Config::set('permission.cache.store', 'array');

    Cache::store('array')->clear();
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

function primePermissionsCache(User $user, \App\Models\Application $application, array $permissions): void
{
    /** @var RbacServiceContract $rbac */
    $rbac = app(RbacServiceContract::class);
    /** @var CacheResolverContract $cache */
    $cache = app(CacheResolverContract::class);

    $role = makeIamRole($application, 'admin');
    $rbac->assignRole($user, $role->name, $application);
    $rbac->syncPermissions($role, $permissions, $application);

    $cache->rememberUserPerms($user, $application);
}

it('caches_user_permissions_per_application', function () {
    $application = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $user = User::factory()->create();
    $permissions = [makeIamPermission($application, 'indicator', 'view')->name];

    primePermissionsCache($user, $application, $permissions);

    $cacheKey = sprintf('perms:%d:%d', $user->getKey(), $application->getKey());

    expect(Cache::store('array')->get($cacheKey))->toMatchArray($permissions);
});

it('invalidates_cache_on_role_assignment_event', function () {
    $application = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $user = User::factory()->create();
    $permissions = [makeIamPermission($application, 'indicator', 'view')->name];

    primePermissionsCache($user, $application, $permissions);

    $listener = app(InvalidateUserRbacCache::class);
    $listener->handle(new AppRoleAssigned($user, 'siimut.admin', $application));

    $cacheKey = sprintf('perms:%d:%d', $user->getKey(), $application->getKey());
    expect(Cache::store('array')->get($cacheKey))->toBeNull();
});

it('invalidates_cache_on_role_revocation_event', function () {
    $application = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $user = User::factory()->create();
    $permissions = [makeIamPermission($application, 'indicator', 'view')->name];

    primePermissionsCache($user, $application, $permissions);

    $listener = app(InvalidateUserRbacCache::class);
    $listener->handle(new AppRoleRevoked($user, 'siimut.admin', $application));

    $cacheKey = sprintf('perms:%d:%d', $user->getKey(), $application->getKey());
    expect(Cache::store('array')->get($cacheKey))->toBeNull();
});

it('invalidates_cache_on_permission_sync_event', function () {
    $application = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $user = User::factory()->create();
    $permissions = [makeIamPermission($application, 'indicator', 'view')->name];

    primePermissionsCache($user, $application, $permissions);

    $role = \Spatie\Permission\Models\Role::where('name', 'siimut.admin')->first();
    $listener = app(InvalidateUserRbacCache::class);
    $listener->handle(new PermissionSynced($role, $permissions, $application));

    $cacheKey = sprintf('perms:%d:%d', $user->getKey(), $application->getKey());
    expect(Cache::store('array')->get($cacheKey))->toBeNull();
});
