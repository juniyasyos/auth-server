<?php

namespace App\Filament\Panel\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ApplicationStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalApplications = DB::table('applications')->whereNull('deleted_at')->count();

        $activeApplications = DB::table('applications')
            ->where('enabled', 1)
            ->whereNull('deleted_at')
            ->count();

        $totalUsers = DB::table('users')->count();

        $activeUsers = DB::table('iam_user_application_roles')
            ->distinct('user_id')
            ->count('user_id');

        return [
            Stat::make('Total Applications', $totalApplications)
                ->description('Total registered applications')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('primary'),

            Stat::make('Active Applications', $activeApplications)
                ->description('Currently enabled applications')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Users', $totalUsers)
                ->description('Total registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Active Users', $activeUsers)
                ->description('Users with application access')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
        ];
    }
}
