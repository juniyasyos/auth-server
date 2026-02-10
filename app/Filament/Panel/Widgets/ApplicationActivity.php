<?php

namespace App\Filament\Panel\Widgets;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class ApplicationActivity extends BaseWidget
{
    protected static ?int $sort = 150;

    protected static ?string $heading = 'Recent Application Activities';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->whereIn('event', ['login', 'created', 'updated', 'deleted'])
                    ->where('description', 'like', '%application%')
                    ->orWhere('properties', 'like', '%application%')
                    ->orderBy('created_at', 'desc')
                    ->limit(20)
            )
            ->columns([
                TextColumn::make('description')
                    ->label('Activity')
                    ->limit(50),

                TextColumn::make('causer.name')
                    ->label('User')
                    ->default('System'),

                TextColumn::make('event')
                    ->label('Action')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'login' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime()
                    ->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No application activities yet')
            ->emptyStateDescription('Application-related activities will appear here.')
            ->emptyStateIcon('heroicon-o-squares-2x2');
    }
}
