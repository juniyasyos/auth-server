<?php

namespace App\Filament\Panel\Resources\Users\Pages;

use App\Filament\Panel\Resources\Users\RelationManagers\AccessProfilesRelationManager;
use App\Filament\Panel\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Guava\FilamentModalRelationManagers\Actions\RelationManagerAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            RelationManagerAction::make()
                ->label('Manage Role Bundles')
                ->record($this->getRecord())
                ->slideOver()
                ->relationManager(AccessProfilesRelationManager::make()),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
