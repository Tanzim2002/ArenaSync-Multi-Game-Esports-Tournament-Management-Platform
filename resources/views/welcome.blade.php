@extends('layouts.app')

@section('title', 'ArenaSync')

@section('content')
<section class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900">
    <div class="grid gap-10 px-6 py-14 lg:grid-cols-[1.2fr_.8fr] lg:px-12 lg:py-20">
        <div>
            <span class="inline-flex rounded-full border border-cyan-800 bg-cyan-950/40 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-cyan-300">
                Esports operations, synchronized
            </span>

            <h1 class="mt-6 max-w-3xl text-5xl font-black tracking-tight text-white sm:text-6xl">
                Run every tournament from
                <span class="text-cyan-400">one arena.</span>
            </h1>

            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">
                ArenaSync connects tournament creation, team registration, payments,
                match scheduling, results, livestreams, leaderboards, sponsorships,
                verification, and performance history in one platform.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
                <a
                    href="{{ route('tournaments.index') }}"
                    class="rounded-lg bg-cyan-600 px-6 py-3 font-semibold text-white transition hover:bg-cyan-500"
                >
                    Browse Tournaments
                </a>

                <a
                    href="{{ route('leaderboard.index') }}"
                    class="rounded-lg border border-slate-700 bg-slate-950 px-6 py-3 font-semibold text-slate-200 transition hover:border-cyan-700 hover:text-cyan-300"
                >
                    View Leaderboard
                </a>

                @guest
                    <a
                        href="{{ route('register') }}"
                        class="rounded-lg border border-slate-700 px-6 py-3 font-semibold text-slate-200 transition hover:border-cyan-700 hover:text-cyan-300"
                    >
                        Create Account
                    </a>
                @endguest
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
            @auth
                <div class="rounded-xl border border-cyan-900 bg-slate-950 p-6">
                    <p class="text-sm text-slate-500">Signed in as</p>

                    <h2 class="mt-1 text-2xl font-bold text-white">
                        {{ auth()->user()->name }}
                    </h2>

                    <p class="mt-1 text-sm font-semibold text-cyan-300">
                        {{ auth()->user()->role }}
                    </p>

                    <a
                        href="{{ route('dashboard.index') }}"
                        class="mt-5 inline-flex rounded-lg bg-cyan-600 px-5 py-2.5 font-semibold text-white transition hover:bg-cyan-500"
                    >
                        Open Dashboard
                    </a>
                </div>

                @if (auth()->user()->hasRole(\App\Models\User::ROLE_ORGANIZER))
                    <div class="rounded-xl border border-slate-800 bg-slate-950 p-6">
                        <h3 class="font-semibold text-white">Organizer Workspace</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-400">
                            Create tournaments, configure pricing, approve participants,
                            verify payments, schedule matches, and manage livestreams.
                        </p>

                        <a
                            href="{{ route('tournaments.manage') }}"
                            class="mt-4 inline-flex text-sm font-semibold text-cyan-400 hover:text-cyan-300"
                        >
                            Manage Tournaments â†’
                        </a>
                    </div>
                @elseif (auth()->user()->hasRole(\App\Models\User::ROLE_PLAYER))
                    <div class="rounded-xl border border-slate-800 bg-slate-950 p-6">
                        <h3 class="font-semibold text-white">Player Workspace</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-400">
                            Build a team, register for tournaments, submit payments,
                            compete in matches, and follow performance history.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-3">
                            <a
                                href="{{ route('teams.create') }}"
                                class="text-sm font-semibold text-cyan-400 hover:text-cyan-300"
                            >
                                Create Team â†’
                            </a>

                            <a
                                href="{{ route('tournaments.index') }}"
                                class="text-sm font-semibold text-cyan-400 hover:text-cyan-300"
                            >
                                Find Tournament â†’
                            </a>
                        </div>
                    </div>
                @else
                    <div class="rounded-xl border border-slate-800 bg-slate-950 p-6">
                        <h3 class="font-semibold text-white">Administration</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-400">
                            Review organizer verification requests and manage platform games.
                        </p>

                        <a
                            href="{{ route('admin.organizer-verifications.index') }}"
                            class="mt-4 inline-flex text-sm font-semibold text-cyan-400 hover:text-cyan-300"
                        >
                            Review Organizers â†’
                        </a>
                    </div>
                @endif
            @else
                <div class="rounded-xl border border-cyan-900 bg-slate-950 p-6">
                    <h2 class="text-2xl font-bold text-white">
                        Ready to compete?
                    </h2>

                    <p class="mt-3 text-sm leading-6 text-slate-400">
                        Join as a player or organizer and start using the complete
                        ArenaSync tournament workflow.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a
                            href="{{ route('login') }}"
                            class="rounded-lg border border-slate-700 px-5 py-2.5 font-semibold text-slate-200 hover:border-cyan-700"
                        >
                            Login
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="rounded-lg bg-cyan-600 px-5 py-2.5 font-semibold text-white hover:bg-cyan-500"
                        >
                            Register
                        </a>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</section>

<section class="mt-10 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    @foreach ([
        ['Tournament Control', 'Create, classify, publish, track lifecycle status, and manage participants.'],
        ['Payments & Eligibility', 'Run free or paid tournaments with submission and organizer verification.'],
        ['Matches & Results', 'Schedule matches, submit results, verify outcomes, and update leaderboards.'],
        ['Community & Broadcast', 'Use chat, announcements, livestream links, sponsors, search, and dashboards.'],
    ] as [$title, $description])
        <article class="rounded-xl border border-slate-800 bg-slate-900 p-5">
            <h3 class="font-semibold text-white">
                {{ $title }}
            </h3>

            <p class="mt-2 text-sm leading-6 text-slate-400">
                {{ $description }}
            </p>
        </article>
    @endforeach
</section>
@endsection