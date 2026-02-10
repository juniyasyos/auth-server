<?php

namespace App\Filament\Panel\Widgets;

use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ApplicationUserChart extends ApexChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Application User Distribution';

    protected function getOptions(): array
    {
        // Get user distribution per application
        $appData = DB::table('iam_user_application_roles')
            ->join('iam_roles', 'iam_user_application_roles.role_id', '=', 'iam_roles.id')
            ->join('applications', 'iam_roles.application_id', '=', 'applications.id')
            ->select(
                'applications.name',
                DB::raw('COUNT(DISTINCT iam_user_application_roles.user_id) as total_users')
            )
            ->whereNull('applications.deleted_at')
            ->groupBy('applications.id', 'applications.name')
            ->orderByDesc('total_users')
            ->get();

        $labels = $appData->pluck('name')->toArray();
        $values = $appData->pluck('total_users')->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Total Users',
                    'data' => $values,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
                'labels' => [
                    'rotate' => -45,
                    'style' => [
                        'fontSize' => '12px',
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Number of Users',
                ],
            ],
            'colors' => ['#3B82F6'],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '70%',
                    'borderRadius' => 4,
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'fontSize' => '12px',
                    'colors' => ['#fff'],
                ],
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
                            'width' => 300,
                        ],
                        'xaxis' => [
                            'labels' => [
                                'rotate' => -90,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
