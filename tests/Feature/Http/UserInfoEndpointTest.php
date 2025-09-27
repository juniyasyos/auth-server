<?php

use App\Models\User;
use App\Services\Contracts\RbacServiceContract;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedUserWithPermissions(User $user, \App\Models\Application $application, array $permissions, string $roleKey = 'admin'): void
{
    /** @var RbacServiceContract $rbac */
    $rbac = app(RbacServiceContract::class);

    $role = makeIamRole($application, $roleKey);
    $rbac->assignRole($user, $role->name, $application);
    $rbac->syncPermissions($role, $permissions, $application);
}

it('returns_profile_and_claims_for_authenticated_user', function () {
    $application = makeIamApplication('siimut');
    $user = User::factory()->create();

    seedUserWithPermissions($user, $application, [makeIamPermission($application, 'indicator', 'view')->name]);

    $response = $this->actingAs($user)->getJson(route('oauth.userinfo'));

    $response->assertOk()
        ->assertJson(fn ($json) => $json
            ->where('sub', (string) $user->getKey())
            ->where('name', $user->name)
            ->where('email', $user->email)
            ->has('claims.apps')
            ->has('claims.roles')
            ->has('claims.perms')
        );
});

it('hides_disabled_applications_from_userinfo', function () {
    $enabled = makeIamApplication('siimut');
    $disabled = makeIamApplication('tamasuma');
    $disabled->update(['enabled' => false]);
    $user = User::factory()->create();

    seedUserWithPermissions($user, $enabled, [makeIamPermission($enabled, 'indicator', 'view')->name]);
    seedUserWithPermissions($user, $disabled, [makeIamPermission($disabled, 'course', 'view')->name], 'editor');

    $response = $this->actingAs($user)->getJson(route('oauth.userinfo'));

    $response->assertOk();
    expect($response->json('claims.apps'))->toContain('siimut')->not->toContain('tamasuma');
});

it('respects_application_filter_query_param', function () {
    $siimut = makeIamApplication('siimut');
    $tamasuma = makeIamApplication('tamasuma');
    $user = User::factory()->create();

    seedUserWithPermissions($user, $siimut, [makeIamPermission($siimut, 'indicator', 'view')->name], 'admin');
    seedUserWithPermissions($user, $tamasuma, [makeIamPermission($tamasuma, 'course', 'manage')->name], 'editor');

    $response = $this->actingAs($user)
        ->getJson(route('oauth.userinfo', ['app' => 'tamasuma']));

    $response->assertOk();
    expect($response->json('claims.apps'))->toMatchArray(['tamasuma']);
    expect($response->json('claims.roles'))->toMatchArray(['tamasuma.editor']);
});
