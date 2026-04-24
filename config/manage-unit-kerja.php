<?php

return [
    'model' => [
        'unit_kerja' => \App\Models\UnitKerja::class,
        'user' => \App\Models\User::class,
    ],

    'filament' => [
        'active' => true,
        'resources' => [
            \App\Filament\Panel\Resources\UnitKerjas\UnitKerjaResource::class,
        ],
    ],

    'app_env' => env('MANAGE_UNIT_KERJA_APP_ENV', env('APP_ENV', 'production')),

    'center_application' => env('MANAGE_UNIT_KERJA_CENTER_APPLICATION', true),

    'app_center_url' => env('MANAGE_UNIT_KERJA_APP_CENTER_URL', ''),

    'sync' => [
        'active' => env('MANAGE_UNIT_KERJA_SYNC_ACTIVE', true),
    ],

    'push' => [
        'active' => env('MANAGE_UNIT_KERJA_PUSH_ACTIVE', true),
        'path' => env('MANAGE_UNIT_KERJA_PUSH_PATH', 'client/push'),
        'middleware' => env('MANAGE_UNIT_KERJA_PUSH_MIDDLEWARE', 'api') ? explode(',', env('MANAGE_UNIT_KERJA_PUSH_MIDDLEWARE', 'api')) : ['api'],
    ],

    'navigation_sort' => 0,
];
