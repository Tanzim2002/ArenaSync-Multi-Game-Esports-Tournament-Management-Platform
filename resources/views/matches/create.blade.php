@extends('layouts.app')

@section('title', 'Schedule Match | ArenaSync')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <a href="{{ route('tournaments.matches.index', $tournament) }}" class="text-cyan-400 hover:text-cyan-300">
                ← Back to match timeline
            </a>
        </div>

        <h1 class="mb-8 text-3xl font-bold text-cyan-400">Schedule Match — {{ $tournament->title }}</h1>

        @if ($teams->isEmpty())
            <div class="rounded-lg border border-amber-700 bg-amber-950/40 p-4 text-amber-200">
                No confirmed participants are available yet. Teams must have an approved registration
                @if ($tournament->is_paid)
                    and a verified payment
                @endif
                before they can be scheduled.
            </div>
        @else
            <div class="rounded-xl border border-slate-800 bg-slate-900 p-6">
                <form method="POST" action="{{ route('tournaments.matches.store', $tournament) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="round" class="mb-2 block font-medium text-slate-200">Round / Stage</label>
                        <input
                            id="round" name="round" type="text" value="{{ old('round') }}" required maxlength="100"
                            placeholder="Example: Quarterfinal"
                            class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
                        >
                        @error('round')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label for="team_one_id" class="mb-2 block font-medium text-slate-200">Team One</label>
                            <select id="team_one_id" name="team_one_id" required class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white">
                                <option value="">Select a team</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}" @selected(old('team_one_id') == $team->id)>{{ $team->name }}</option>
                                @endforeach
                            </select>
                            @error('team_one_id')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="team_two_id" class="mb-2 block font-medium text-slate-200">Team Two (leave blank for a bye)</label>
                            <select id="team_two_id" name="team_two_id" class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white">
                                <option value="">Bye / TBD</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}" @selected(old('team_two_id') == $team->id)>{{ $team->name }}</option>
                                @endforeach
                            </select>
                            @error('team_two_id')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="scheduled_at" class="mb-2 block font-medium text-slate-200">Scheduled Date & Time</label>
                        <input
                            id="scheduled_at" name="scheduled_at" type="datetime-local" value="{{ old('scheduled_at') }}" required
                            class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
                        >
                        @error('scheduled_at')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="rounded bg-cyan-600 px-6 py-3 font-semibold text-white hover:bg-cyan-500">
                        Schedule Match
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection