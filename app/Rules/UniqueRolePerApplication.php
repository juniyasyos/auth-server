<?php

namespace App\Rules;

use App\Domain\Iam\Models\ApplicationRole;
use Illuminate\Contracts\Validation\Rule;

class UniqueRolePerApplication implements Rule
{
    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        if ($value === null) {
            return true;
        }

        $roleIds = is_array($value) ? $value : (is_iterable($value) ? (array) $value : []);

        if (empty($roleIds)) {
            return true;
        }

        $roles = ApplicationRole::query()
            ->whereIn('id', $roleIds)
            ->get();

        return $roles->groupBy('application_id')->every(fn ($group) => $group->count() <= 1);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'Hanya satu role per aplikasi yang diperbolehkan.';
    }
}
