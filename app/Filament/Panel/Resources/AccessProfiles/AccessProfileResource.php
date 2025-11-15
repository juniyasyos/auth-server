<?php

namespace App\Filament\Panel\Resources\AccessProfiles;

use App\Filament\Panel\Resources\AccessProfiles\Pages\CreateAccessProfile;
use App\Filament\Panel\Resources\AccessProfiles\Pages\EditAccessProfile;
use App\Filament\Panel\Resources\AccessProfiles\Pages\ListAccessProfiles;
use App\Filament\Panel\Resources\AccessProfiles\Schemas\AccessProfileForm;
use App\Filament\Panel\Resources\AccessProfiles\Tables\AccessProfilesTable;
use App\Models\AccessProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AccessProfileResource extends Resource
{
    protected static ?string $model = AccessProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name ';

    public static function form(Schema $schema): Schema
    {
        return AccessProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccessProfilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccessProfiles::route('/'),
            'create' => CreateAccessProfile::route('/create'),
            'edit' => EditAccessProfile::route('/{record}/edit'),
        ];
    }
}
