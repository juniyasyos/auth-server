<?php

namespace App\Filament\Panel\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
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
            // performa & UX
            ->defaultSort('updated_at', 'desc')
            ->paginated([10, 25, 50, 100, 'all']) // opsi bawaan bisa dikustom, v4 mendukung ini
            ->defaultPaginationPageOption(25)
            // kolom
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->weight('semibold')
                    ->description(fn(User $record) => $record->email)
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                // toggle inline untuk cepat aktif/nonaktif
                ToggleColumn::make('active')
                    ->label('Active')
                    ->onColor('success')
                    ->offColor('secondary')
                    ->afterStateUpdated(fn(User $record, bool $state) => $record->refresh())
                    ->sortable()
                    ->toggleable(),

                // total roles & apps (agar ringan & ringkas)
                TextColumn::make('roles_count')
                    ->counts('roles')
                    ->label('Roles')
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('applications_count')
                    ->counts('applications')
                    ->label('Apps')
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                // indikator keamanan umum (contoh: 2FA kolom opsional)
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
            // filter cerdas
            ->filters([
                TernaryFilter::make('active')->label('Active'),

                // filter by role (Spatie)
                SelectFilter::make('roles')
                    ->label('Role')
                    ->multiple()
                    ->relationship('roles', 'name'),

                // filter by Application (IAM Gate)
                SelectFilter::make('applications')
                    ->label('Application')
                    ->multiple()
                    ->relationship('applications', 'name'),

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
                        if (! empty($data['from'])) $i[] = 'Updated from ' . $data['from'];
                        if (! empty($data['until'])) $i[] = 'Updated until ' . $data['until'];
                        return $i;
                    }),

                // aktifkan jika User memakai SoftDeletes
                // TrashedFilter::make(),
            ])
            // aksi tiap record
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ImpersonateTableAction::make()
                    ->label('Impersonate')
                    ->visible(fn(User $record) => Auth::id() !== $record->id) // jangan impersonate diri sendiri
                // ->guard('web') // opsional
                // ->redirectTo(route('home')) // opsional: ke app utama
                ,
            ])
            // toolbar / bulk actions
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
                    // RestoreBulkAction::make(),
                    // ForceDeleteBulkAction::make(),
                ]),
            ])
            // empty state yang ramah
            ->emptyStateHeading('Belum ada user')
            ->emptyStateDescription('Tambahkan user atau ubah filter pencarian untuk melihat data.');
    }
}
