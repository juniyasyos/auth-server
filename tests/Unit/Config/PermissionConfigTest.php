<?php

uses(Tests\TestCase::class);

it('config_teams_is_enabled_and_uses_application_id_key', function () {
    expect(config('permission.teams'))->toBeTrue();
    expect(config('permission.column_names.team_foreign_key'))->toBe('application_id');
    expect(config('permission.team_model'))->toBe(App\Models\Application::class);
    expect(config('permission.teams_strict_check'))->toBeTrue();
});
