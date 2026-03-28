<?php

namespace App\Filament\Panel\Resources\UnitKerjas\RelationManagers;

use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Juniyasyos\ManageUnitKerja\Filament\Resources\UnitKerjaResource\RelationManagers\UsersRelationManager as RelationManagersUsersRelationManager;

class UsersRelationUnitKerjaManager extends RelationManagersUsersRelationManager
{
    protected static string $relationship = 'users';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Split::make([
                    ImageColumn::make('avatar_url')
                        ->searchable()
                        ->circular()
                        ->grow(false)
                        ->getStateUsing(fn($record) => $record->avatar_url ?: "https://ui-avatars.com/api/?name=" . urlencode($record->name)),
                    Stack::make([
                        TextColumn::make('name')
                            ->label(__('filament-forms::users.fields.name'))
                            ->searchable()
                            ->weight(FontWeight::Bold),
                    ])->alignStart()->space(1),
                    Stack::make([
                        TextColumn::make('roles.name')
                            ->label(__('filament-forms::users.fields.roles'))
                            ->searchable()
                            ->icon('heroicon-o-shield-check')
                            ->grow(false),
                        TextColumn::make('nip')
                            ->label(__('filament-forms::users.fields.email'))
                            ->icon('heroicon-m-finger-print')
                            ->searchable()
                            ->grow(false),
                    ])->alignStart()->visibleFrom('lg')->space(1)
                ])
            ])
            ->headerActions([
                AttachAction::make()
                    ->color('primary')
                    ->schema(fn() => [
                        Select::make('recordId')
                            ->options(function () {
                                $relatedIds = $this->getRelationship()->pluck('id')->toArray();

                                return User::whereNotIn('id', $relatedIds)
                                    ->get()
                                    ->mapWithKeys(fn($user) => [
                                        $user->id => $user->name,
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->attachAnother(false)
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name']),
            ])
            ->actions([
                DetachAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
