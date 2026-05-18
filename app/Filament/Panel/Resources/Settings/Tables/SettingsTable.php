<?php

namespace App\Filament\Panel\Resources\Settings\Tables;

use App\Models\Setting;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Database Settings')
            ->description('Kelola value setting yang sudah dikunci key dan category-nya.')
            ->defaultSort('category')
            ->striped()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->searchPlaceholder('Cari key, category, atau value setting...')
            ->columns([
                TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('semibold')
                    ->description(fn (Setting $record) => self::descriptionFor($record)),

                TextColumn::make('category')
                    ->label('Category')
                    ->getStateUsing(fn (Setting $record): string => $record->category ?: 'general')
                    ->badge()
                    ->sortable(),

                TextColumn::make('value')
                    ->label('Value')
                    ->getStateUsing(fn (Setting $record): string => self::isSensitive($record)
                        ? '***masked***'
                        : self::previewValue($record))
                    ->wrap()
                    ->limit(60)
                    ->tooltip(fn (Setting $record): ?string => self::isSensitive($record) ? 'Sensitive value disembunyikan' : null),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Category')
                    ->options(config('settings.groups', [])),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                // Delete is intentionally disabled. Settings are fixed records.
            ]);
    }

    private static function previewValue(Setting $record): string
    {
        $type = config("settings.definitions.{$record->key}.type", 'string');

        return match ($type) {
            'boolean' => $record->getValue() ? 'true' : 'false',
            'array', 'json' => json_encode($record->getValue(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '[]',
            default => (string) $record->getValue(),
        };
    }

    private static function descriptionFor(Setting $record): ?string
    {
        return config("settings.definitions.{$record->key}.description");
    }

    private static function isSensitive(Setting $record): bool
    {
        return (bool) config("settings.definitions.{$record->key}.is_sensitive", false);
    }
}
