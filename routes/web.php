<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Public Game List
|--------------------------------------------------------------------------
*/

Route::resource('games', GameController::class)
    ->only([
        'index',
    ]);

/*
|--------------------------------------------------------------------------
| Administrator Game Management
|--------------------------------------------------------------------------
|
| Static routes such as /games/create must be registered before
| the dynamic /games/{game} details route.
|
*/

Route::middleware(['auth', 'role:ADMIN'])->group(function (): void {
    Route::resource('games', GameController::class)
        ->except([
            'index',
            'show',
        ]);
});

/*
|--------------------------------------------------------------------------
| Public Game Details
|--------------------------------------------------------------------------
*/

Route::resource('games', GameController::class)
    ->only([
        'show',
    ]);

require __DIR__.'/auth.php';