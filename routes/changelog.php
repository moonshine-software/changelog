<?php

use Illuminate\Support\Facades\Route;
use MoonShine\ChangeLog\Http\Controllers\RestoreController;

Route::prefix(config('moonshine.route.prefix', ''))
    ->middleware('moonshine')
    ->as('moonshine.')->group(static function (): void {
        Route::middleware(config('moonshine.auth.middleware', []))->group(function (): void {
            Route::post(
                '/resource/{resourceUri}/changelog/{changeLog}/{resourceItem}',
                RestoreController::class,
            )->name('changelog');
        });
    });
