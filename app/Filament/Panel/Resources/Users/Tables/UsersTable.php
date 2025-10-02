<?php

namespace App\Filament\Panel\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use STS\FilamentImpersonate\Actions\Impersonate as ImpersonateTableAction;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25)
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->weight('semibold')
                    ->description(fn(User $record) => $record->email)
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                ToggleColumn::make('active')
                    ->label('Active')
                    ->onColor('success')
                    ->offColor('secondary')
                    ->afterStateUpdated(fn(User $record, bool $state) => $record->refresh())
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('mfa_enabled')
                    ->label('MFA')
                    ->boolean()
                    ->getStateUsing(fn(User $record) => ! empty($record->two_factor_secret ?? null))
                    ->tooltip(fn(User $record) => ! empty($record->two_factor_secret ?? null) ? 'Enabled' : 'Disabled')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_login_at')
                    ->label('Last login')
                    ->since()
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('active')->label('Active'),
                Filter::make('updated_between')
                    ->label('Updated between')
                    ->schema([
                        DatePicker::make('from')->native(false),
                        DatePicker::make('until')->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn(Builder $q, $date) => $q->whereDate('updated_at', '>=', $date))
                            ->when($data['until'] ?? null, fn(Builder $q, $date) => $q->whereDate('updated_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $i = [];
                        if (! empty($data['from'])) {
                            $i[] = 'Updated from ' . $data['from'];
                        }
                        if (! empty($data['until'])) {
                            $i[] = 'Updated until ' . $data['until'];
                        }

                        return $i;
                    }),
                // TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ImpersonateTableAction::make()
                    ->label('Impersonate')
                    ->visible(fn(User $record) => Auth::id() !== $record->id),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-m-bolt')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->update(['active' => true])),
                    BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-m-no-symbol')
                        ->color('secondary')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->update(['active' => false])),
                    DeleteBulkAction::make(),
                    // ForceDeleteBulkAction::make(),
                    // RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada user')
            ->emptyStateDescription('Tambahkan user atau ubah filter pencarian untuk melihat data.');
    }
}
