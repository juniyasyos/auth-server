<?php

namespace App\Filament\Panel\Widgets;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class RecentActivities extends BaseWidget
{
    public static ?string $heading = 'Recent Activities';

    protected static ?int $sort = 100;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->whereIn('event', ['login', 'created', 'updated', 'deleted'])
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
            )
            ->columns([
                TextColumn::make('causer.name')
                    ->label('User')
                    ->default('System')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Action')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'login' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn($state) => str($state)->afterLast('\\')->toString())
                    ->searchable(),

                TextColumn::make('log_name')
                    ->label('Category')
                    ->formatStateUsing(fn($state) => ucfirst($state ?? 'general')),

                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->since()
                    ->tooltip(fn($state) => $state?->format('Y-m-d H:i:s')),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10]);
    }
}
