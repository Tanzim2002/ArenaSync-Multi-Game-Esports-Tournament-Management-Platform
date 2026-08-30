@extends('layouts.app')

@section('title', 'Dashboard - ArenaSync')

@section('content')

    {{-- Page Heading --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-cyan-400">
            ArenaSync Dashboard
        </h1>

        <p class="mt-2 text-slate-300">
            Welcome, {{ $user->name }}.
            You are logged in as {{ $user->role }}.
        </p>
    </div>


    {{-- Role-Based Dashboard Statistics --}}
    <section class="mb-10">
        <h2 class="mb-4 text-2xl font-semibold">
            Your Dashboard
        </h2>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">

            {{-- PLAYER Dashboard --}}
            @if ($user->hasRole(\App\Models\User::ROLE_PLAYER))

                <div class="rounded-lg border border-slate-700 bg-slate-900 p-5">
                    <p class="text-sm text-slate-400">
                        Teams Led
                    </p>

                    <p class="mt-2 text-3xl font-bold text-cyan-400">
                        {{ $dashboard['teams_led'] ?? 0 }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-700 bg-slate-900 p-5">
                    <p class="text-sm text-slate-400">
                        Team Memberships
                    </p>

                    <p class="mt-2 text-3xl font-bold text-cyan-400">
                        {{ $dashboard['team_memberships'] ?? 0 }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-700 bg-slate-900 p-5">
                    <p class="text-sm text-slate-400">
                        Tournament Registrations
                    </p>

                    <p class="mt-2 text-3xl font-bold text-cyan-400">
                        {{ $dashboard['registrations'] ?? 0 }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-700 bg-slate-900 p-5">
                    <p class="text-sm text-slate-400">
                        Upcoming Matches
                    </p>

                    <p class="mt-2 text-3xl font-bold text-cyan-400">
                        {{ $dashboard['upcoming_matches'] ?? 0 }}
                    </p>
                </div>

            @endif


            {{-- ORGANIZER Dashboard --}}
            @if ($user->hasRole(\App\Models\User::ROLE_ORGANIZER))

                <div class="rounded-lg border border-slate-700 bg-slate-900 p-5">
                    <p class="text-sm text-slate-400">
                        My Tournaments
                    </p>

                    <p class="mt-2 text-3xl font-bold text-cyan-400">
                        {{ $dashboard['tournaments'] ?? 0 }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-700 bg-slate-900 p-5">
                    <p class="text-sm text-slate-400">
                        Registration Open
                    </p>

                    <p class="mt-2 text-3xl font-bold text-cyan-400">
                        {{ $dashboard['open_tournaments'] ?? 0 }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-700 bg-slate-900 p-5">
                    <p class="text-sm text-slate-400">
                        Pending Registrations
                    </p>

                    <p class="mt-2 text-3xl font-bold text-cyan-400">
                        {{ $dashboard['pending_registrations'] ?? 0 }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-700 bg-slate-900 p-5">
                    <p class="text-sm text-slate-400">
                        Scheduled Matches
                    </p>

                    <p class="mt-2 text-3xl font-bold text-cyan-400">
                        {{ $dashboard['scheduled_matches'] ?? 0 }}
                    </p>
                </div>

            @endif


            {{-- ADMIN Dashboard --}}
            @if ($user->hasRole(\App\Models\User::ROLE_ADMIN))

                <div class="rounded-lg border border-slate-700 bg-slate-900 p-5">
                    <p class="text-sm text-slate-400">
                        Total Users
                    </p>

                    <p class="mt-2 text-3xl font-bold text-cyan-400">
                        {{ $dashboard['users'] ?? 0 }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-700 bg-slate-900 p-5">
                    <p class="text-sm text-slate-400">
                        Games
                    </p>

                    <p class="mt-2 text-3xl font-bold text-cyan-400">
                        {{ $dashboard['games'] ?? 0 }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-700 bg-slate-900 p-5">
                    <p class="text-sm text-slate-400">
                        Teams
                    </p>

                    <p class="mt-2 text-3xl font-bold text-cyan-400">
                        {{ $dashboard['teams'] ?? 0 }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-700 bg-slate-900 p-5">
                    <p class="text-sm text-slate-400">
                        Tournaments
                    </p>

                    <p class="mt-2 text-3xl font-bold text-cyan-400">
                        {{ $dashboard['tournaments'] ?? 0 }}
                    </p>
                </div>

            @endif

        </div>
    </section>


    {{-- Search and Filters --}}
    <section class="mb-10 rounded-lg border border-slate-700 bg-slate-900 p-6">

        <h2 class="mb-5 text-2xl font-semibold">
            Search & Filter
        </h2>

        <form
            method="GET"
            action="{{ route('dashboard.index') }}"
            class="grid gap-4 md:grid-cols-2 lg:grid-cols-3"
        >

            {{-- Search --}}
            <div>
                <label
                    for="search"
                    class="mb-2 block text-sm text-slate-300"
                >
                    Search
                </label>

                <input
                    id="search"
                    name="search"
                    type="text"
                    value="{{ $search }}"
                    placeholder="Search games, teams, players..."
                    class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-2"
                >
            </div>


            {{-- Search Type --}}
            <div>
                <label
                    for="type"
                    class="mb-2 block text-sm text-slate-300"
                >
                    Search Type
                </label>

                <select
                    id="type"
                    name="type"
                    class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-2"
                >
                    <option
                        value="all"
                        {{ $type === 'all' ? 'selected' : '' }}
                    >
                        All
                    </option>

                    <option
                        value="games"
                        {{ $type === 'games' ? 'selected' : '' }}
                    >
                        Games
                    </option>

                    <option
                        value="teams"
                        {{ $type === 'teams' ? 'selected' : '' }}
                    >
                        Teams
                    </option>

                    <option
                        value="players"
                        {{ $type === 'players' ? 'selected' : '' }}
                    >
                        Players
                    </option>

                    <option
                        value="tournaments"
                        {{ $type === 'tournaments' ? 'selected' : '' }}
                    >
                        Tournaments
                    </option>
                </select>
            </div>


            {{-- Game Filter --}}
            <div>
                <label
                    for="game_id"
                    class="mb-2 block text-sm text-slate-300"
                >
                    Game
                </label>

                <select
                    id="game_id"
                    name="game_id"
                    class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-2"
                >
                    <option value="">
                        All Games
                    </option>

                    @foreach ($filterGames as $filterGame)
                        <option
                            value="{{ $filterGame->id }}"
                            {{
                                (string) $gameId === (string) $filterGame->id
                                    ? 'selected'
                                    : ''
                            }}
                        >
                            {{ $filterGame->name }}
                        </option>
                    @endforeach
                </select>
            </div>


            {{-- Tournament Status --}}
            <div>
                <label
                    for="status"
                    class="mb-2 block text-sm text-slate-300"
                >
                    Tournament Status
                </label>

                <select
                    id="status"
                    name="status"
                    class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-2"
                >
                    <option value="">
                        All Statuses
                    </option>

                    @foreach (\App\Models\Tournament::statuses() as $tournamentStatus)
                        @if ($tournamentStatus !== \App\Models\Tournament::STATUS_DRAFT)
                            <option
                                value="{{ $tournamentStatus }}"
                                {{ $status === $tournamentStatus ? 'selected' : '' }}
                            >
                                {{ str_replace('_', ' ', $tournamentStatus) }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>


            {{-- Region --}}
            <div>
                <label
                    for="region"
                    class="mb-2 block text-sm text-slate-300"
                >
                    Region
                </label>

                <select
                    id="region"
                    name="region"
                    class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-2"
                >
                    <option value="">
                        All Regions
                    </option>

                    @foreach ($regions as $regionOption)
                        <option
                            value="{{ $regionOption }}"
                            {{ $region === $regionOption ? 'selected' : '' }}
                        >
                            {{ $regionOption }}
                        </option>
                    @endforeach
                </select>
            </div>


            {{-- Buttons --}}
            <div class="flex items-end gap-3">

                <button
                    type="submit"
                    class="rounded bg-cyan-600 px-5 py-2 font-semibold hover:bg-cyan-500"
                >
                    Search
                </button>

                <a
                    href="{{ route('dashboard.index') }}"
                    class="rounded border border-slate-600 px-5 py-2 hover:bg-slate-800"
                >
                    Reset
                </a>

            </div>

        </form>
    </section>


    {{-- Games Results --}}
    @if (in_array($type, ['all', 'games'], true))
        <section class="mb-10">

            <h2 class="mb-4 text-2xl font-semibold">
                Games
            </h2>

            @forelse ($games as $game)

                <div class="mb-3 rounded-lg border border-slate-700 bg-slate-900 p-5">

                    <div class="flex items-center justify-between gap-4">

                        <div>
                            <h3 class="text-lg font-semibold text-cyan-400">
                                {{ $game->name }}
                            </h3>

                            <p class="mt-1 text-sm text-slate-300">
                                Genre:
                                {{ $game->genre ?? 'N/A' }}
                            </p>

                            <p class="text-sm text-slate-300">
                                Platform:
                                {{ $game->platform ?? 'N/A' }}
                            </p>

                            <p class="text-sm text-slate-300">
                                Tournaments:
                                {{ $game->tournaments_count }}
                            </p>
                        </div>

                        <a
                            href="{{ route('games.show', $game) }}"
                            class="rounded border border-cyan-500 px-4 py-2 text-sm hover:bg-cyan-950"
                        >
                            View Game
                        </a>

                    </div>
                </div>

            @empty

                <p class="text-slate-400">
                    No games found.
                </p>

            @endforelse

        </section>
    @endif


    {{-- Team Results --}}
    @if (in_array($type, ['all', 'teams'], true))
        <section class="mb-10">

            <h2 class="mb-4 text-2xl font-semibold">
                Teams
            </h2>

            @forelse ($teams as $team)

                <div class="mb-3 rounded-lg border border-slate-700 bg-slate-900 p-5">

                    <div class="flex items-center justify-between gap-4">

                        <div>
                            <h3 class="text-lg font-semibold text-cyan-400">
                                {{ $team->name }}
                            </h3>

                            <p class="mt-1 text-sm text-slate-300">
                                Leader:
                                {{ $team->leader?->name ?? 'N/A' }}
                            </p>

                            <p class="text-sm text-slate-300">
                                Members:
                                {{ $team->team_members_count }}
                            </p>

                            @if ($team->description)
                                <p class="mt-2 text-sm text-slate-400">
                                    {{ $team->description }}
                                </p>
                            @endif
                        </div>

                        <a
                            href="{{ route('teams.show', $team) }}"
                            class="rounded border border-cyan-500 px-4 py-2 text-sm hover:bg-cyan-950"
                        >
                            View Team
                        </a>

                    </div>
                </div>

            @empty

                <p class="text-slate-400">
                    No teams found.
                </p>

            @endforelse

        </section>
    @endif


    {{-- Player Results --}}
    @if (in_array($type, ['all', 'players'], true))
        <section class="mb-10">

            <h2 class="mb-4 text-2xl font-semibold">
                Players
            </h2>

            @forelse ($players as $player)

                <div class="mb-3 rounded-lg border border-slate-700 bg-slate-900 p-5">

                    <div class="flex items-center justify-between gap-4">

                        <div>
                            <h3 class="text-lg font-semibold text-cyan-400">
                                {{ $player->name }}
                            </h3>

                            <p class="mt-1 text-sm text-slate-300">
                                {{ $player->email }}
                            </p>
                        </div>

                        <a
                            href="{{ route('performance.players.show', $player) }}"
                            class="rounded border border-cyan-500 px-4 py-2 text-sm hover:bg-cyan-950"
                        >
                            Performance
                        </a>

                    </div>
                </div>

            @empty

                <p class="text-slate-400">
                    No players found.
                </p>

            @endforelse

        </section>
    @endif


    {{-- Tournament Results --}}
    @if (in_array($type, ['all', 'tournaments'], true))
        <section class="mb-10">

            <h2 class="mb-4 text-2xl font-semibold">
                Tournaments
            </h2>

            @forelse ($tournaments as $tournament)

                <div class="mb-3 rounded-lg border border-slate-700 bg-slate-900 p-5">

                    <div class="flex items-center justify-between gap-4">

                        <div>
                            <h3 class="text-lg font-semibold text-cyan-400">
                                {{ $tournament->title }}
                            </h3>

                            <p class="mt-1 text-sm text-slate-300">
                                Game:
                                {{ $tournament->game?->name ?? 'N/A' }}
                            </p>

                            <p class="text-sm text-slate-300">
                                Organizer:
                                {{ $tournament->organizer?->name ?? 'N/A' }}
                            </p>

                            <p class="text-sm text-slate-300">
                                Region:
                                {{ $tournament->region }}
                            </p>

                            <p class="text-sm text-slate-300">
                                Status:
                                {{ str_replace('_', ' ', $tournament->status) }}
                            </p>

                            <p class="text-sm text-slate-300">
                                Start:
                                {{ $tournament->start_at?->format('d M Y, h:i A') }}
                            </p>
                        </div>

                        <a
                            href="{{ route('tournaments.show', $tournament) }}"
                            class="rounded border border-cyan-500 px-4 py-2 text-sm hover:bg-cyan-950"
                        >
                            View Tournament
                        </a>

                    </div>
                </div>

            @empty

                <p class="text-slate-400">
                    No tournaments found.
                </p>

            @endforelse

        </section>
    @endif


    {{-- Existing Feature Links --}}
    <section class="border-t border-slate-700 pt-6">

        <h2 class="mb-4 text-xl font-semibold">
            ArenaSync Tools
        </h2>

        <div class="flex flex-wrap gap-3">

            <a
                href="{{ route('games.index') }}"
                class="rounded bg-slate-800 px-4 py-2 hover:bg-slate-700"
            >
                Games
            </a>

            <a
                href="{{ route('tournaments.index') }}"
                class="rounded bg-slate-800 px-4 py-2 hover:bg-slate-700"
            >
                Tournaments
            </a>

            <a
                href="{{ route('leaderboard.index') }}"
                class="rounded bg-slate-800 px-4 py-2 hover:bg-slate-700"
            >
                Leaderboard
            </a>

        </div>
    </section>

@endsection