<?php

use App\Http\Controllers\MatchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Feature 12 - Match contracts consumed by other members (per your
| GitHub guide's API contracts table: GET /api/matches/:id, GET /api/matches/upcoming)
|--------------------------------------------------------------------------
*/

Route::get('/matches/upcoming', [MatchController::class, 'apiUpcoming']);
Route::get('/matches/{match}', [MatchController::class, 'apiShow']);