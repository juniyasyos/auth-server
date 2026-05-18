<?php

namespace App\Filament\Panel\Resources\Settings\Schemas;

use App\Models\Setting;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Setting')
                    ->description('Key dan category dikunci. Hanya value yang bisa diubah.')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        Placeholder::make('key_display')
                            ->label('Key')
                            ->content(fn (?Setting $record): string => $record?->key ?? '-'),

                        Placeholder::make('category_display')
                            ->label('Category')
                            ->content(fn (?Setting $record): string => $record?->category ?? '-'),

                        Textarea::make('value')
                            ->label('Value')
                            ->rows(5)
                            ->columnSpanFull()
                    ]),
            ]);
    }
}
