<?php

use App\Http\Controllers\UserInfoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

// Root route - redirect based on auth status
Route::get('/', function () {
    return auth()->check()
        ? Redirect::to('/dashboard')
        : Redirect::to('/login');
})->name('home');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

$ssoRoutes = require __DIR__ . '/sso.php';

if (is_array($ssoRoutes) && isset($ssoRoutes['web']) && is_callable($ssoRoutes['web'])) {
    $ssoRoutes['web']();
}

if (app()->environment('testing')) {
    require __DIR__ . '/testing.php';
}
