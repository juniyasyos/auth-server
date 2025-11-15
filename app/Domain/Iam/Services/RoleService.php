<?php

namespace App\Domain\Iam\Services;

use App\Domain\Iam\Models\Application;
use App\Domain\Iam\Models\ApplicationRole;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RoleService
{
    /**
     * Create a new role for an application.
     *
     * @throws \Exception
     */
    public function createRole(
        string $appKey,
        string $slug,
        string $name,
        ?string $description = null,
        bool $isSystem = false
    ): ApplicationRole {
        $application = Application::findByKey($appKey);

        if (! $application->enabled) {
            throw new \Exception("Application '{$appKey}' is not enabled.");
        }

        // Normalize slug
        $slug = Str::slug($slug, '_');

        // Check if role already exists
        $existing = ApplicationRole::where('application_id', $application->id)
            ->where('slug', $slug)
            ->first();

        if ($existing) {
            throw new \Exception("Role '{$slug}' already exists for application '{$appKey}'.");
        }

        return ApplicationRole::create([
            'application_id' => $application->id,
            'slug' => $slug,
            'name' => $name,
            'description' => $description,
            'is_system' => $isSystem,
        ]);
    }

    /**
     * Update an existing role.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws \Exception
     */
    public function updateRole(ApplicationRole $role, array $data): ApplicationRole
    {
        // Protect system roles from slug changes
        if ($role->is_system && isset($data['slug']) && $data['slug'] !== $role->slug) {
            throw new \Exception('Cannot change slug of a system role.');
        }

        // If slug is being changed, ensure uniqueness
        if (isset($data['slug']) && $data['slug'] !== $role->slug) {
            $slug = Str::slug($data['slug'], '_');
            $existing = ApplicationRole::where('application_id', $role->application_id)
                ->where('slug', $slug)
                ->where('id', '!=', $role->id)
                ->exists();

            if ($existing) {
                throw new \Exception("Role '{$slug}' already exists for this application.");
            }

            $data['slug'] = $slug;
        }

        $role->update($data);

        return $role->fresh();
    }

    /**
     * Delete a role.
     *
     * @throws \Exception
     */
    public function deleteRole(ApplicationRole $role): void
    {
        if ($role->is_system) {
            throw new \Exception('Cannot delete a system role.');
        }

        // Check if role has users
        $userCount = $role->users()->count();
        if ($userCount > 0) {
            throw new \Exception("Cannot delete role. It is assigned to {$userCount} user(s).");
        }

        $role->delete();
    }

    /**
     * Get all roles for an application.
     */
    public function getRolesForApplication(string $appKey): Collection
    {
        $application = Application::findByKey($appKey);

        return ApplicationRole::where('application_id', $application->id)
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Find role by application and slug.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findRoleBySlug(string $appKey, string $slug): ApplicationRole
    {
        $application = Application::findByKey($appKey);

        return ApplicationRole::where('application_id', $application->id)
            ->where('slug', Str::slug($slug, '_'))
            ->firstOrFail();
    }

    /**
     * Find role by ID.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findRoleById(int $roleId): ApplicationRole
    {
        return ApplicationRole::findOrFail($roleId);
    }

    /**
     * Get all system roles for an application.
     */
    public function getSystemRoles(string $appKey): Collection
    {
        $application = Application::findByKey($appKey);

        return ApplicationRole::where('application_id', $application->id)
            ->where('is_system', true)
            ->orderBy('name')
            ->get();
    }
}
