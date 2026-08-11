<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMembershipRequestController;
use App\Http\Controllers\TeamMemberController;
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
| Team Management
|--------------------------------------------------------------------------
*/

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

    /*
    |--------------------------------------------------------------------------
    | Feature 5 - Team Invitations / Join Requests
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/teams/{team}/invitations',
        [TeamMembershipRequestController::class, 'invite']
    )->name('teams.invitations.store');

    Route::post(
        '/teams/{team}/join-requests',
        [TeamMembershipRequestController::class, 'requestToJoin']
    )->name('teams.join-requests.store');

    Route::patch(
        '/team-membership-requests/{membershipRequest}/accept',
        [TeamMembershipRequestController::class, 'accept']
    )->name('team-membership-requests.accept');

    Route::patch(
        '/team-membership-requests/{membershipRequest}/reject',
        [TeamMembershipRequestController::class, 'reject']
    )->name('team-membership-requests.reject');

    Route::patch(
        '/team-membership-requests/{membershipRequest}/cancel',
        [TeamMembershipRequestController::class, 'cancel']
    )->name('team-membership-requests.cancel');

    /*
    |--------------------------------------------------------------------------
    | Feature 6 - Team Roles / Leave Team
    |--------------------------------------------------------------------------
    */

    Route::patch(
        '/teams/{team}/members/{teamMember}/role',
        [TeamMemberController::class, 'updateRole']
    )->name('teams.members.role.update');

    Route::delete(
        '/teams/{team}/leave',
        [TeamMemberController::class, 'leave']
    )->name('teams.leave');
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