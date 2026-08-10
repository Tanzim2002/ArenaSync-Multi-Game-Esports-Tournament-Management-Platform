<?php

use App\Http\Controllers\Payments\TournamentPricingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Member 03 — Payment & Match Setup module routes.
| Included from routes/web.php and routes/api.php.
| Only M3 routes belong here.
|--------------------------------------------------------------------------
*/

// ---- Sprint 1: F9 Free/Paid Tournament Setup (web) ----
Route::middleware(['auth'])->group(function () {
    Route::get('/tournaments/{tournament}/pricing/edit', [TournamentPricingController::class, 'edit'])
        ->name('tournaments.pricing.edit');

    Route::put('/tournaments/{tournament}/pricing', [TournamentPricingController::class, 'update'])
        ->name('tournaments.pricing.update');
});

use App\Http\Controllers\Payments\LivestreamController;

// ---- Sprint 2: F13 Livestream Link Management ----
Route::get('/tournaments/{tournament}/livestreams', [LivestreamController::class, 'index'])
    ->name('tournaments.livestreams.index'); // public

Route::middleware(['auth'])->group(function () {
    Route::get('/tournaments/{tournament}/livestreams/create', [LivestreamController::class, 'create'])
        ->name('tournaments.livestreams.create');
    Route::post('/tournaments/{tournament}/livestreams', [LivestreamController::class, 'store'])
        ->name('tournaments.livestreams.store');
    Route::get('/tournaments/{tournament}/livestreams/{livestream}/edit', [LivestreamController::class, 'edit'])
        ->name('tournaments.livestreams.edit');
    Route::put('/tournaments/{tournament}/livestreams/{livestream}', [LivestreamController::class, 'update'])
        ->name('tournaments.livestreams.update');
    Route::delete('/tournaments/{tournament}/livestreams/{livestream}', [LivestreamController::class, 'destroy'])
        ->name('tournaments.livestreams.destroy');
});