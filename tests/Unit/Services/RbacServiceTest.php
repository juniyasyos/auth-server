<?php

use App\Models\User;
use App\Services\Contracts\RbacServiceContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Tests\Support\Concerns\SetsCurrentApplication;

uses(Tests\TestCase::class, RefreshDatabase::class, SetsCurrentApplication::class);

beforeEach(function () {
    Config::set('cache.default', 'array');
    Config::set('permission.cache.store', 'array');

    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('assigns_and_revokes_role_per_application', function () {
    $application = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $user = User::factory()->create();
    $role = makeIamRole($application, 'admin');

    /** @var RbacServiceContract $service */
    $service = app(RbacServiceContract::class);

    $service->assignRole($user, $role->name, $application);
    expect($service->userRoles($user, $application))->toContain($role->name);

    $service->revokeRole($user, $role->name, $application);
    expect($service->userRoles($user, $application))->not->toContain($role->name);
});

it('syncs_permissions_for_role_scoped_to_application', function () {
    $application = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $user = User::factory()->create();
    $role = makeIamRole($application, 'editor');
    $permissions = [
        makeIamPermission($application, 'indicator', 'view')->name,
        makeIamPermission($application, 'indicator', 'update')->name,
    ];

    /** @var RbacServiceContract $service */
    $service = app(RbacServiceContract::class);

    $service->assignRole($user, $role->name, $application);
    $service->syncPermissions($role, $permissions, $application);

    $teamColumn = Config::get('permission.column_names.team_foreign_key');
    $pivot = Config::get('permission.table_names.role_has_permissions');

    $pivotRows = DB::table($pivot)
        ->where($teamColumn, $application->getKey())
        ->where('role_id', $role->getKey())
        ->pluck('permission_id');

    expect($pivotRows)->toHaveCount(2);
    expect($service->userPermissions($user, $application))->toMatchArray($permissions);
});

it('checks_user_permissions_scoped_by_application', function () {
    $siimut = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $tamasuma = $this->createTenantApplication(['app_key' => 'tamasuma']);
    $user = User::factory()->create();

    $adminRole = makeIamRole($siimut, 'admin');
    $viewPermission = makeIamPermission($siimut, 'indicator', 'view')->name;
    $otherPermission = makeIamPermission($tamasuma, 'course', 'manage')->name;
    $otherRole = makeIamRole($tamasuma, 'admin');

    /** @var RbacServiceContract $service */
    $service = app(RbacServiceContract::class);

    $service->assignRole($user, $adminRole->name, $siimut);
    $service->syncPermissions($adminRole, [$viewPermission], $siimut);

    $service->assignRole($user, $otherRole->name, $tamasuma);
    $service->syncPermissions($otherRole, [$otherPermission], $tamasuma);

    expect($service->userPermissions($user, $siimut))->toContain($viewPermission)
        ->not->toContain($otherPermission);
    expect($service->userPermissions($user, $tamasuma))->toContain($otherPermission)
        ->not->toContain($viewPermission);
});

it('enforces_naming_convention', function () {
    $application = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $user = User::factory()->create();

    /** @var RbacServiceContract $service */
    $service = app(RbacServiceContract::class);

    expect(fn () => $service->assignRole($user, 'admin', $application))
        ->toThrow(InvalidArgumentException::class);
    expect(fn () => $service->can($user, $application, 'indicator.view'))
        ->toThrow(InvalidArgumentException::class);
});

it('does_not_leak_roles_between_applications', function () {
    $siimut = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $tamasuma = $this->createTenantApplication(['app_key' => 'tamasuma']);
    $user = User::factory()->create();

    $role = makeIamRole($siimut, 'admin');
    makeIamRole($tamasuma, 'admin');

    /** @var RbacServiceContract $service */
    $service = app(RbacServiceContract::class);

    $service->assignRole($user, $role->name, $siimut);

    expect($service->userRoles($user, $siimut))->toContain($role->name);
    expect($service->userRoles($user, $tamasuma))->toBeEmpty();
});

it('handles_strict_teams_mode', function () {
    $siimut = $this->useApplicationContext($this->createTenantApplication(['app_key' => 'siimut']));
    $tamasuma = $this->createTenantApplication(['app_key' => 'tamasuma']);
    $user = User::factory()->create();

    $role = makeIamRole($siimut, 'admin');
    $permission = makeIamPermission($siimut, 'indicator', 'view')->name;
    makeIamPermission($tamasuma, 'indicator', 'view');

    /** @var RbacServiceContract $service */
    $service = app(RbacServiceContract::class);

    $service->assignRole($user, $role->name, $siimut);
    $service->syncPermissions($role, [$permission], $siimut);

    expect($service->can($user, $siimut, $permission))->toBeTrue();
    expect($service->can($user, $tamasuma, 'tamasuma.indicator.view'))->toBeFalse();
});
