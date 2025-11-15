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
        $this->tempRoleIds = $data['role_ids'] ?? [];

        unset($data['role_ids']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        if (! empty($this->tempRoleIds) && $record) {
            $record->roles()->sync($this->tempRoleIds);
        }

        parent::afterCreate();
    }
}
