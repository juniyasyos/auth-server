<?php

namespace App\Filament\Panel\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('active', true)->count();
        $inactiveUsers = User::where('active', false)->count();

        $activePercentage = $totalUsers > 0
            ? round(($activeUsers / $totalUsers) * 100, 1)
            : 0;

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('All registered users in the system')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->icon('heroicon-o-user-group'),

            Stat::make('Active Users', $activeUsers)
                ->description($activePercentage . '% of total users')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Inactive Users', $inactiveUsers)
                ->description((100 - $activePercentage) . '% of total users')
                ->descriptionIcon('heroicon-m-no-symbol')
                ->color('danger')
                ->icon('heroicon-o-no-symbol'),
        ];
    }
}
