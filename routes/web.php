<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\OrganizerVerificationController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TeamMembershipRequestController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TournamentMessageController;
use App\Http\Controllers\SponsorController;
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

Route::middleware([
    'auth',
    'role:ADMIN',
])->group(function (): void {
    Route::resource('games', GameController::class)
        ->except([
            'index',
            'show',
        ]);
});

/*
|--------------------------------------------------------------------------
| Feature 19 - Organizer Verification
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:ORGANIZER',
])
    ->prefix('organizer')
    ->name('organizer-verification.')
    ->group(function (): void {
        Route::get(
            '/verification',
            [
                OrganizerVerificationController::class,
                'show',
            ]
        )->name('show');

        Route::post(
            '/verification',
            [
                OrganizerVerificationController::class,
                'store',
            ]
        )->name('store');
    });

Route::middleware([
    'auth',
    'role:ADMIN',
])
    ->prefix('admin')
    ->name('admin.organizer-verifications.')
    ->group(function (): void {
        Route::get(
            '/organizer-verifications',
            [
                OrganizerVerificationController::class,
                'index',
            ]
        )->name('index');

        Route::patch(
            '/organizer-verifications/{verification}/approve',
            [
                OrganizerVerificationController::class,
                'approve',
            ]
        )->name('approve');

        Route::patch(
            '/organizer-verifications/{verification}/reject',
            [
                OrganizerVerificationController::class,
                'reject',
            ]
        )->name('reject');
    });

/*
|--------------------------------------------------------------------------
| Team Management
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function (): void {
    Route::get(
        '/teams/create',
        [TeamController::class, 'create']
    )->name('teams.create');

    Route::get(
        '/teams/{team}',
        [TeamController::class, 'show']
    )->name('teams.show');

    Route::get(
        '/teams/{team}/edit',
        [TeamController::class, 'edit']
    )->name('teams.edit');

    Route::post(
        '/teams',
        [TeamController::class, 'store']
    )->name('teams.store');

    Route::put(
        '/teams/{team}',
        [TeamController::class, 'update']
    )->name('teams.update');

    /*
    |--------------------------------------------------------------------------
    | Feature 5 - Team Invitations / Join Requests
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/teams/{team}/invitations',
        [
            TeamMembershipRequestController::class,
            'invite',
        ]
    )->name('teams.invitations.store');

    Route::post(
        '/teams/{team}/join-requests',
        [
            TeamMembershipRequestController::class,
            'requestToJoin',
        ]
    )->name('teams.join-requests.store');

    Route::patch(
        '/team-membership-requests/{membershipRequest}/accept',
        [
            TeamMembershipRequestController::class,
            'accept',
        ]
    )->name('team-membership-requests.accept');

    Route::patch(
        '/team-membership-requests/{membershipRequest}/reject',
        [
            TeamMembershipRequestController::class,
            'reject',
        ]
    )->name('team-membership-requests.reject');

    Route::patch(
        '/team-membership-requests/{membershipRequest}/cancel',
        [
            TeamMembershipRequestController::class,
            'cancel',
        ]
    )->name('team-membership-requests.cancel');

    /*
    |--------------------------------------------------------------------------
    | Feature 6 - Team Roles / Leave Team
    |--------------------------------------------------------------------------
    */

    Route::patch(
        '/teams/{team}/members/{teamMember}/role',
        [
            TeamMemberController::class,
            'updateRole',
        ]
    )->name('teams.members.role.update');

    Route::delete(
        '/teams/{team}/leave',
        [
            TeamMemberController::class,
            'leave',
        ]
    )->name('teams.leave');
});

/*
|--------------------------------------------------------------------------
| Public Tournament List
|--------------------------------------------------------------------------
*/

Route::get(
    '/tournaments',
    [TournamentController::class, 'index']
)->name('tournaments.index');

/*
|--------------------------------------------------------------------------
| Organizer Tournament Management - F2 / F3 / F8
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:ORGANIZER',
])
    ->prefix('tournaments')
    ->name('tournaments.')
    ->group(function (): void {
        Route::get(
            '/manage',
            [
                TournamentController::class,
                'manage',
            ]
        )->name('manage');

        Route::get(
            '/create',
            [
                TournamentController::class,
                'create',
            ]
        )->name('create');

        Route::post(
            '/',
            [
                TournamentController::class,
                'store',
            ]
        )->name('store');

        Route::get(
            '/{tournament}/edit',
            [
                TournamentController::class,
                'edit',
            ]
        )->name('edit');

        Route::match(
            [
                'put',
                'patch',
            ],
            '/{tournament}',
            [
                TournamentController::class,
                'update',
            ]
        )->name('update');

        Route::patch(
            '/{tournament}/publish',
            [
                TournamentController::class,
                'publish',
            ]
        )->name('publish');

        /*
        |--------------------------------------------------------------------------
        | Feature 3 - Tournament Status Tracking
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/{tournament}/status',
            [
                TournamentController::class,
                'changeStatus',
            ]
        )->name('status.update');

        Route::patch(
            '/{tournament}/cancel',
            [
                TournamentController::class,
                'cancel',
            ]
        )->name('cancel');

        /*
        |--------------------------------------------------------------------------
        | Feature 8 - Participant Approval System
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/{tournament}/registrations',
            [
                RegistrationController::class,
                'index',
            ]
        )->name('registrations.index');

        Route::patch(
            '/{tournament}/registrations/{registration}/approve',
            [
                RegistrationController::class,
                'approve',
            ]
        )->name('registrations.approve');

        Route::patch(
            '/{tournament}/registrations/{registration}/reject',
            [
                RegistrationController::class,
                'reject',
            ]
        )->name('registrations.reject');
    });

/*
|--------------------------------------------------------------------------
| Feature 7 - Tournament Registration
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:PLAYER',
])
    ->prefix('tournaments')
    ->name('tournaments.')
    ->group(function (): void {
        Route::get(
            '/{tournament}/register',
            [
                RegistrationController::class,
                'create',
            ]
        )->name('registrations.create');

        Route::post(
            '/{tournament}/register',
            [
                RegistrationController::class,
                'store',
            ]
        )->name('registrations.store');
    });

/*
|--------------------------------------------------------------------------
| Feature 18 - Tournament Chat & Announcements
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('tournaments')
    ->name('tournaments.')
    ->group(function (): void {
        Route::get(
            '/{tournament}/chat',
            [TournamentMessageController::class, 'index']
        )->name('chat');

        Route::post(
            '/{tournament}/chat',
            [TournamentMessageController::class, 'storeChat']
        )->name('chat.store');

        Route::post(
            '/{tournament}/announcements',
            [TournamentMessageController::class, 'storeAnnouncement']
        )->name('announcements.store');
    });
/*
|--------------------------------------------------------------------------
| Public Tournament Details
|--------------------------------------------------------------------------
*/

Route::get(
    '/tournaments/{tournament}',
    [
        TournamentController::class,
        'show',
    ]
)->name('tournaments.show');

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
Route::resource('sponsors', SponsorController::class);
