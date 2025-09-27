<?php

namespace Tests;

use App\Models\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'permission.enable_teams' => true,
            'permission.teams' => true,
            'permission.team_foreign_key' => 'application_id',
            'permission.team_model' => Application::class,
            'permission.teams_strict_check' => true,
        ]);

        app(PermissionRegistrar::class)->setPermissionsTeamId(null);
    }
}
