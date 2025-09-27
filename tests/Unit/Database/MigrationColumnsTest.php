<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('migrations_include_application_id_on_required_pivots', function () {
    expect(Schema::hasColumn('roles', 'application_id'))->toBeTrue();
    expect(Schema::hasColumn('model_has_roles', 'application_id'))->toBeTrue();
    expect(Schema::hasColumn('model_has_permissions', 'application_id'))->toBeTrue();
    expect(Schema::hasColumn('role_has_permissions', 'application_id'))->toBeTrue();
});
