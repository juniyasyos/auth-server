<?php

use App\Models\User;
use App\Services\Contracts\RbacServiceContract;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('switches_team_context_and_changes_authorization_result', function () {
    $siimut = makeIamApplication('siimut');
    $tamasuma = makeIamApplication('tamasuma');
    $user = User::factory()->create();

    /** @var RbacServiceContract $rbac */
    $rbac = app(RbacServiceContract::class);

    $role = makeIamRole($siimut, 'admin');
    $permission = makeIamPermission($siimut, 'indicator', 'view')->name;

    $rbac->assignRole($user, $role->name, $siimut);
    $rbac->syncPermissions($role, [$permission], $siimut);

    $user->setCurrentApplication($siimut);
    expect($user->can($permission))->toBeTrue();

    $user->setCurrentApplication($tamasuma);
    expect($user->can($permission))->toBeFalse();
});
