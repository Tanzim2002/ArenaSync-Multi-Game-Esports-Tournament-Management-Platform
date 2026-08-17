@extends('layouts.app')

@section('title', 'Register for ' . $tournament->title . ' | ArenaSync')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <a
                href="{{ route('tournaments.show', $tournament) }}"
                class="text-cyan-400 hover:text-cyan-300"
            >
                ← Back to tournament
            </a>
        </div>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-cyan-400">
                Tournament Registration
            </h1>

            <p class="mt-2 text-slate-400">
                Register one of your teams for {{ $tournament->title }}.
            </p>
        </div>

        @if ($errors->has('registration'))
            <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
                {{ $errors->first('registration') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="rounded-xl border border-slate-800 bg-slate-900 p-6">
                    <h2 class="text-xl font-semibold text-white">
                        Select Your Team
                    </h2>

                    @if ($teams->isEmpty())
                        <div class="mt-6 rounded-lg border border-amber-700 bg-amber-950/40 p-4 text-amber-200">
                            <p class="font-semibold">
                                No eligible team found
                            </p>

                            <p class="mt-1 text-sm text-amber-300">
                                You must be the leader of a team before you can submit a tournament registration.
                            </p>

                            <a
                                href="{{ route('teams.create') }}"
                                class="mt-4 inline-block rounded bg-cyan-600 px-4 py-2 font-semibold text-white hover:bg-cyan-500"
                            >
                                Create Team
                            </a>
                        </div>
                    @else
                        <form
                            method="POST"
                            action="{{ route('tournaments.registrations.store', $tournament) }}"
                            class="mt-6"
                        >
                            @csrf

                            <div>
                                <label
                                    for="team_id"
                                    class="mb-2 block font-semibold text-slate-200"
                                >
                                    Team
                                </label>

                                <select
                                    id="team_id"
                                    name="team_id"
                                    required
                                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-cyan-500 focus:outline-none"
                                >
                                    <option value="">
                                        Select a team
                                    </option>

                                    @foreach ($teams as $team)
                                        <option
                                            value="{{ $team->id }}"
                                            @selected(old('team_id') == $team->id)
                                            @disabled(in_array($team->id, $registeredTeamIds))
                                        >
                                            {{ $team->name }}

                                            @if (in_array($team->id, $registeredTeamIds))
                                                — Already registered
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                @error('team_id')
                                    <p class="mt-2 text-sm text-red-400">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="mt-6 rounded-lg border border-slate-800 bg-slate-950 p-4">
                                <p class="text-sm text-slate-400">
                                    Your registration will initially be submitted as
                                    <span class="font-semibold text-amber-300">
                                        PENDING
                                    </span>.
                                    Tournament approval is handled separately by the organizer.
                                </p>
                            </div>

                            <button
                                type="submit"
                                class="mt-6 rounded bg-cyan-600 px-5 py-3 font-semibold text-white hover:bg-cyan-500"
                            >
                                Submit Registration
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <aside class="rounded-xl border border-slate-800 bg-slate-900 p-6">
                <h2 class="text-xl font-semibold text-white">
                    Tournament Info
                </h2>

                <div class="mt-5 space-y-4 text-sm">
                    <div>
                        <p class="text-slate-500">
                            Tournament
                        </p>

                        <p class="font-semibold text-slate-200">
                            {{ $tournament->title }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">
                            Game
                        </p>

                        <p class="font-semibold text-slate-200">
                            {{ $tournament->game->name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">
                            Status
                        </p>

                        <p class="font-semibold text-slate-200">
                            {{ str_replace('_', ' ', $tournament->status) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">
                            Registration Deadline
                        </p>

                        <p class="font-semibold text-slate-200">
                            {{ $tournament->registration_deadline->format('M d, Y h:i A') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">
                            Team Limit
                        </p>

                        <p class="font-semibold text-slate-200">
                            {{ $activeRegistrationCount }} / {{ $tournament->team_limit }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">
                            Available Slots
                        </p>

                        <p class="font-semibold text-slate-200">
                            {{ max(0, $tournament->team_limit - $activeRegistrationCount) }}
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection