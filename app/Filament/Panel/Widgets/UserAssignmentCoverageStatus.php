<?php

namespace App\Filament\Panel\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

                // =========================
                // TOTAL USER
                // =========================
                Stat::make('Total Pengguna', number_format($totalUsers))
                    ->description('Jumlah seluruh akun dalam sistem')
                    ->descriptionIcon('heroicon-m-users')
                    ->color('primary')
                    ->icon('heroicon-o-user-group'),

                // =========================
                // SUDAH TERKONFIGURASI
                // =========================
                Stat::make('Sudah Memiliki Akses', number_format($assignedUsers))
                    ->description("{$coveragePercentage}% sudah siap digunakan")
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color('success')
                    ->icon('heroicon-o-check-badge')
                    ->chart([
                        $assignedUsers,
                        $unassignedUsers,
                    ]),

                // =========================
                // BELUM TERKONFIGURASI
                // =========================
                Stat::make('Belum Memiliki Akses', number_format($unassignedUsers))
                    ->description(
                        $unassignedUsers > 0
                            ? 'Perlu assignment akses'
                            : 'Semua user sudah terkonfigurasi'
                    )
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color($unassignedUsers > 0 ? 'warning' : 'success')
                    ->icon('heroicon-o-exclamation-triangle'),
            ];
        } catch (\Throwable $e) {
            Log::error('UserAssignmentCoverageStatus widget failed: ' . $e->getMessage(), ['exception' => $e]);

            return [
                Stat::make('Coverage Status', 0)
                    ->description('Gagal mengambil data assignment')
                    ->color('danger'),
            ];
        }
    }
}
