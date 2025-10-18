<?php

use App\Http\Controllers\Sso\SsoRedirectController;
use App\Http\Controllers\Sso\SsoVerifyController;
use Illuminate\Support\Facades\Route;

return [
    'web' => function (): void {
        Route::middleware('auth')
            ->group(function (): void {
                Route::get('/sso/redirect', SsoRedirectController::class)
                    ->name('sso.redirect');
            });
    },
    'api' => function (): void {
        Route::post('/sso/verify', SsoVerifyController::class)
            ->name('api.sso.verify');
    },
];
