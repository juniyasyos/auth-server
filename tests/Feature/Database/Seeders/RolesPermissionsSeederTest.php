<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => Database\Seeders\ApplicationsSeeder::class]);
});

it('seeds_roles_and_permissions_with_correct_naming', function () {
    Artisan::call('db:seed', ['--class' => Database\Seeders\RolesPermissionsSeeder::class]);

    $roleNames = Role::pluck('name');
    $permissionNames = Permission::pluck('name');

    $roleNames->each(function ($name) {
        expect(Str::contains($name, '.'))->toBeTrue();
    });

    $permissionNames->each(function ($name) {
        expect(substr_count($name, '.'))->toBe(2);
    });
});

it('roles_permissions_seeder_is_idempotent', function () {
    Artisan::call('db:seed', ['--class' => Database\Seeders\RolesPermissionsSeeder::class]);
    $roles = Role::count();
    $permissions = Permission::count();

    Artisan::call('db:seed', ['--class' => Database\Seeders\RolesPermissionsSeeder::class]);

    expect(Role::count())->toBe($roles);
    expect(Permission::count())->toBe($permissions);
});
