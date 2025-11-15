<?php

namespace App\Filament\Panel\Resources\AccessProfiles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\MultiSelect;
use Filament\Schemas\Schema;

class AccessProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Toggle::make('is_system')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                MultiSelect::make('role_ids')
                    ->label('Assigned Roles (Application - Role)')
                    ->options(function () {
                        return \App\Domain\Iam\Models\ApplicationRole::with('application')
                            ->get()
                            ->mapWithKeys(function ($role) {
                                $appName = $role->application?->name ?? $role->application_id;

                                return [$role->id => $appName.' — '.$role->name];
                            })->toArray();
                    })
                    ->preload()
                    ->columnSpanFull(),
            ]);
    }
}
