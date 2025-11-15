<?php

namespace App\Filament\Panel\Resources\Users\RelationManagers;

use App\Filament\Panel\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class ApplicationRolesRelationManager extends RelationManager
{
    protected static string $relationship = 'applicationRoles';

    protected static ?string $relatedResource = UserResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
