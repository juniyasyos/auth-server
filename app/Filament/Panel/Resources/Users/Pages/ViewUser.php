<?php

namespace App\Filament\Panel\Resources\Users\Pages;

use App\Filament\Panel\Resources\Users\RelationManagers\AccessProfilesRelationManager;
use App\Filament\Panel\Resources\Users\UserResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentModalRelationManagers\Actions\RelationManagerAction;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            RelationManagerAction::make()
                ->label('Manage Role Bundles')
                ->record($this->getRecord())
                ->slideOver()
                ->relationManager(AccessProfilesRelationManager::make()),
        ];
    }
}
