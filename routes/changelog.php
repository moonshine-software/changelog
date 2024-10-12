<?php

use Illuminate\Support\Facades\Route;
use MoonShine\ChangeLog\Http\Controllers\RestoreController;

Route::moonshine(static function () {
    Route::post(
        '/changelog/{changeLog}/{resourceItem}',
        RestoreController::class,
    )->name('changelog');
}, withResource: true, withAuthenticate: true);
