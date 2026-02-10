<?php

namespace App\Filament\Panel\Widgets;

use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ApplicationUsage extends ApexChartWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Application Usage';

    protected function getOptions(): array
    {
        // Get application usage statistics
        $appData = DB::table('iam_user_application_roles')
            ->join('iam_roles', 'iam_user_application_roles.role_id', '=', 'iam_roles.id')
            ->join('applications', 'iam_roles.application_id', '=', 'applications.id')
            ->select('applications.name', DB::raw('COUNT(DISTINCT iam_user_application_roles.user_id) as user_count'))
            ->whereNull('applications.deleted_at')
            ->groupBy('applications.id', 'applications.name')
            ->orderByDesc('user_count')
            ->get();

        $labels = $appData->pluck('name')->toArray();
        $values = $appData->pluck('user_count')->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Active Users',
                    'data' => $values,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
            ],
            'colors' => [
                '#3B82F6', // Blue
                '#10B981', // Green
                '#F59E0B', // Amber
                '#EF4444', // Red
                '#8B5CF6', // Purple
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '55%',
                    'endingShape' => 'rounded',
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
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
