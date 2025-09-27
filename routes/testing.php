<?php

use Illuminate\Support\Facades\Route;

Route::get('/testing/secure', fn () => response('ok'))
    ->middleware('ensure.app.permission:siimut,siimut.indicator.view')
    ->name('testing.secure');
