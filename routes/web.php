<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    Route::get('/teams/create', [TeamController::class, 'create'])
        ->name('teams.create');

    Route::get('/teams/{team}', [TeamController::class, 'show'])
        ->name('teams.show');

    Route::get('/teams/{team}/edit', [TeamController::class, 'edit'])
        ->name('teams.edit');

    Route::post('/teams', [TeamController::class, 'store'])
        ->name('teams.store');

    Route::put('/teams/{team}', [TeamController::class, 'update'])
        ->name('teams.update');
});

require __DIR__.'/auth.php';