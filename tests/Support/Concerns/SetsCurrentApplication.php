<?php

namespace Tests\Support\Concerns;

use App\Models\Application;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Spatie\Permission\PermissionRegistrar;

trait SetsCurrentApplication
{
    protected function createTenantApplication(array $attributes = []): Application
    {
        return Application::factory()->create($attributes);
    }

    protected function useApplicationContext(?Application $application = null): Application
    {
        $application ??= $this->createTenantApplication();

        App::make(PermissionRegistrar::class)->setPermissionsTeamId($application->getKey());

        return $application;
    }

    protected function clearApplicationContext(): void
    {
        App::make(PermissionRegistrar::class)->setPermissionsTeamId(null);
    }

    protected function setUserApplication(User $user, Application $application): void
    {
        if (method_exists($user, 'setCurrentApplication')) {
            $user->setCurrentApplication($application);
        }
    }
}
