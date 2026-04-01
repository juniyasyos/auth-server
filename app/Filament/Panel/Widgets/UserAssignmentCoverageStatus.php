<?php

namespace App\Filament\Panel\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class UserAssignmentCoverageStatus extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        try {
            $totalUsers = DB::table('users')->count();
            $assignedUsers = DB::table('users')
                ->whereIn('id', DB::table('user_access_profiles')->select('user_id'))
                ->count();
            $unassignedUsers = $totalUsers - $assignedUsers;
            $coveragePercentage = $totalUsers > 0
                ? round(($assignedUsers / $totalUsers) * 100, 1)
                : 0;

            return [
                Stat::make('Total Users', $totalUsers)
                    ->description('All registered users')
                    ->descriptionIcon('heroicon-m-users')
                    ->color('primary')
                    ->icon('heroicon-o-user-group'),

                Stat::make('Assigned Users', $assignedUsers)
                    ->description($coveragePercentage . '% with access profile')
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color('success')
                    ->icon('heroicon-o-check-badge'),

                Stat::make('Unassigned Users', $unassignedUsers)
                    ->description('Missing access profile assignment')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color($unassignedUsers > 0 ? 'warning' : 'success')
                    ->icon('heroicon-o-exclamation-triangle'),
            ];
        } catch (\Throwable $e) {
            \Log::error('UserAssignmentCoverageStatus widget failed: ' . $e->getMessage(), ['exception' => $e]);

            return [
                Stat::make('Coverage Status', 0)
                    ->description('Unable to fetch assignment data')
                    ->color('danger'),
            ];
        }
    }
}
