<?php

use Illuminate\Support\Facades\Route;
use MarceliTo\Aicms\Http\Controllers\ChatController;

Route::middleware(config('aicms.middleware', ['web', 'auth']))
    ->prefix(config('aicms.route_prefix', 'aicms'))
    ->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('aicms.index');
    });
