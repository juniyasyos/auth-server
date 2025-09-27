<?php

use App\Models\User;
use App\Services\Contracts\ClaimsBuilderContract;
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

function buildClaims(User $user, ?\App\Models\Application $application = null): array
{
    /** @var ClaimsBuilderContract $builder */
    $builder = app(ClaimsBuilderContract::class);

    return $builder->build($user, $application);
}

function assignRoleWithPermissions(User $user, \App\Models\Application $application, string $roleKey, array $permissions): void
{
    /** @var RbacServiceContract $rbac */
    $rbac = app(RbacServiceContract::class);

    $role = makeIamRole($application, $roleKey);
    $rbac->assignRole($user, $role->name, $application);
    $rbac->syncPermissions($role, $permissions, $application);
}

it('builds_claims_for_single_application', function () {
    $application = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $user = User::factory()->create();
    $permissions = [makeIamPermission($application, 'indicator', 'view')->name];

    assignRoleWithPermissions($user, $application, 'admin', $permissions);

    $claims = buildClaims($user);

    expect($claims['apps'])->toMatchArray(['siimut']);
    expect($claims['roles'])->toHaveCount(1);
    expect($claims['roles'][0])->toStartWith('siimut.');
    expect($claims['perms'])->toMatchArray($permissions);
});

it('builds_claims_for_multiple_applications', function () {
    $siimut = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $tamasuma = $this->createTenantApplication(['app_key' => 'tamasuma']);
    $user = User::factory()->create();

    assignRoleWithPermissions($user, $siimut, 'admin', [makeIamPermission($siimut, 'indicator', 'view')->name]);
    assignRoleWithPermissions($user, $tamasuma, 'editor', [makeIamPermission($tamasuma, 'course', 'manage')->name]);

    $claims = buildClaims($user);

    expect($claims['apps'])->toMatchArray(['siimut', 'tamasuma']);
    expect($claims['roles'])->toContain('siimut.admin')->toContain('tamasuma.editor');
    expect($claims['perms'])->toContain('siimut.indicator.view')->toContain('tamasuma.course.manage');
});

it('filters_claims_when_application_specified', function () {
    $application = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $user = User::factory()->create();
    $permissions = [makeIamPermission($application, 'indicator', 'view')->name];

    assignRoleWithPermissions($user, $application, 'viewer', $permissions);

    $claims = buildClaims($user, $application);

    expect($claims['apps'])->toMatchArray(['siimut']);
    expect($claims['app_key'])->toBe('siimut');
    expect($claims['application_id'])->toBe($application->getKey());
    expect($claims['perms'])->toMatchArray($permissions);
});

it('produces_stable_structure', function () {
    $application = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $user = User::factory()->create();

    assignRoleWithPermissions($user, $application, 'viewer', [makeIamPermission($application, 'indicator', 'view')->name]);

    $claims = buildClaims($user);

    expect($claims)->toHaveKeys(['sub', 'apps', 'roles', 'perms']);
});

it('excludes_disabled_applications_from_claims', function () {
    $enabled = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $disabled = $this->createTenantApplication(['app_key' => 'tamasuma', 'enabled' => false]);
    $user = User::factory()->create();

    assignRoleWithPermissions($user, $enabled, 'admin', [makeIamPermission($enabled, 'indicator', 'view')->name]);
    assignRoleWithPermissions($user, $disabled, 'admin', [makeIamPermission($disabled, 'course', 'view')->name]);

    $claims = buildClaims($user);

    expect($claims['apps'])->toContain('siimut')->not->toContain('tamasuma');
    expect($claims['roles'])->toContain('siimut.admin')->not->toContain('tamasuma.admin');
});
