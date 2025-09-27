<?php

use App\Http\Controllers\UserInfoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Vue dashboard routes are disabled - using Filament panel as main dashboard
// Route::redirect('dashboard', 'vue/dashboard')->middleware(['auth', 'verified']);
// Route::get('vue/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

if (app()->environment('testing')) {
    require __DIR__.'/testing.php';
}

Route::middleware('auth')
    ->get('/oauth/userinfo', UserInfoController::class)
    ->name('oauth.userinfo');
