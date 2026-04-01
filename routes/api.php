<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UserInfoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Register general API routes here. SSO specific routes are configured in
| routes/sso.php.
|
*/

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected auth routes
Route::middleware('auth:api')->prefix('auth')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

// Protected user routes (SSO JWT)
Route::middleware('sso.jwt')->group(function () {
    Route::get('/users/me', UserInfoController::class);
    Route::get('/user', UserInfoController::class);
    Route::get('/users/applications', [UserInfoController::class, 'applications'])
        ->name('users.applications');
    Route::get('/users/applications/detail', [UserInfoController::class, 'applicationsDetail'])
        ->name('users.applications.detail');
});

$ssoRoutes = require __DIR__ . '/sso.php';

if (is_array($ssoRoutes) && isset($ssoRoutes['api']) && is_callable($ssoRoutes['api'])) {
    $ssoRoutes['api']();
}
