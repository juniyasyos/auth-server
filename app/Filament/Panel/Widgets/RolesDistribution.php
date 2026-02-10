<?php

namespace App\Filament\Panel\Widgets;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class RolesDistribution extends ApexChartWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Roles Distribution';

    protected function getOptions(): array
    {
        // Get roles distribution using IAM tables
        $rolesData = DB::table('users')
            ->join('iam_user_application_roles', 'users.id', '=', 'iam_user_application_roles.user_id')
            ->join('iam_roles', 'iam_user_application_roles.role_id', '=', 'iam_roles.id')
            ->select('iam_roles.name', DB::raw('COUNT(DISTINCT users.id) as total'))
            ->groupBy('iam_roles.name')
            ->orderByDesc('total')
            ->get();

        $labels = $rolesData->pluck('name')->toArray();
        $values = $rolesData->pluck('total')->toArray();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => $values,
            'labels' => $labels,
            'colors' => [
                '#3B82F6', // Blue
                '#10B981', // Green
                '#F59E0B', // Amber
                '#EF4444', // Red
                '#8B5CF6', // Purple
                '#EC4899', // Pink
                '#14B8A6', // Teal
                '#6366F1', // Indigo
            ],
            'legend' => [
                'position' => 'bottom',
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => 'function(value) { return value + " users"; }',
                ],
            ],
            'responsive' => [
                [
                    'breakpoint' => 480,
                    'options' => [
                        'chart' => [
                            'width' => 200,
                        ],
                        'legend' => [
                            'position' => 'bottom',
                        ],
                    ],
                ],
            ],
        ];
    }
}
