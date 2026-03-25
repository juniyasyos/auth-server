<?php

namespace App\Filament\Panel\Resources\AccessProfiles\Pages;

use App\Filament\Panel\Resources\AccessProfiles\AccessProfileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccessProfile extends CreateRecord
{
    protected static string $resource = AccessProfileResource::class;

    /**
     * Temporary place to hold role ids selected in the form.
     */
    protected array $tempRoleIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $roleIds = isset($data['roles']) ? (array) $data['roles'] : (isset($data['role_ids']) ? (array) $data['role_ids'] : []);

        $this->tempRoleIds = array_values(array_unique(array_filter($roleIds)));

        unset($data['roles'], $data['role_ids']);

        return $data;
    }

    // when Filament calls the `afterCreate` hook it will invoke this method
    // from the base page. we only need to sync the temporary role ids that
    // were removed from the form data, there is no parent implementation so
    // calling `parent::afterCreate()` causes Livewire to blow up with a
    // missing method exception. the hook may be protected, but Filament
    // invokes it internally via callHook(), so visibility is fine.
    protected function afterCreate(): void
    {
        $record = $this->record;

        if (! empty($this->tempRoleIds) && $record) {
            $record->roles()->sync($this->tempRoleIds);
        }

        // intentionally do not call parent, base class has no afterCreate
    }
}
