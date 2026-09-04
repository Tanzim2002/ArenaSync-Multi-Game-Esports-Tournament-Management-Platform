@extends('layouts.app')

@section('title', 'Submit Match Result | ArenaSync')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <a
            href="{{ route('matches.show', $match) }}"
            class="text-sm font-medium text-cyan-400 hover:text-cyan-300"
        >
            â† Back to Match Details
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-cyan-400">
            Submit Match Result
        </h1>

        <p class="mt-2 text-slate-400">
            {{ $match->tournament?->title ?? 'Tournament' }}
            Â· {{ $match->round }}
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
            <p class="font-semibold">Please correct the following:</p>

            <ul class="mt-2 list-inside list-disc space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($result && $result->isRejected())
        <div class="mb-6 rounded-lg border border-red-800 bg-red-950/40 p-4 text-red-200">
            <p class="font-semibold">
                Previous submission was rejected.
            </p>

            @if ($result->review_notes)
                <p class="mt-2 text-sm">
                    Organizer note: {{ $result->review_notes }}
                </p>
            @endif

            <p class="mt-2 text-sm">
                Correct the result and submit it again.
            </p>
        </div>
    @elseif ($result && $result->isPending())
        <div class="mb-6 rounded-lg border border-amber-800 bg-amber-950/40 p-4 text-amber-200">
            A result is already pending organizer verification. You can update it
            until it is verified.
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('match-results.store', $match) }}"
        class="rounded-xl border border-slate-800 bg-slate-900 p-6"
    >
        @csrf

        <div class="grid items-center gap-5 md:grid-cols-[1fr_auto_1fr]">
            <div class="rounded-xl border border-slate-800 bg-slate-950 p-6 text-center">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Team One
                </p>

                <h2 class="mt-2 text-xl font-bold text-white">
                    {{ $match->teamOne?->name ?? 'Team One' }}
                </h2>

                <input
                    type="number"
                    name="team_one_score"
                    min="0"
                    required
                    value="{{ old('team_one_score', $result?->team_one_score ?? 0) }}"
                    class="mx-auto mt-5 w-28 rounded-lg border border-slate-700 bg-slate-900 px-4 py-3 text-center text-2xl font-bold text-white outline-none focus:border-cyan-500"
                >
            </div>

            <div class="text-center font-bold text-slate-500">
                VS
            </div>

            <div class="rounded-xl border border-slate-800 bg-slate-950 p-6 text-center">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Team Two
                </p>

                <h2 class="mt-2 text-xl font-bold text-white">
                    {{ $match->teamTwo?->name ?? 'Team Two' }}
                </h2>

                <input
                    type="number"
                    name="team_two_score"
                    min="0"
                    required
                    value="{{ old('team_two_score', $result?->team_two_score ?? 0) }}"
                    class="mx-auto mt-5 w-28 rounded-lg border border-slate-700 bg-slate-900 px-4 py-3 text-center text-2xl font-bold text-white outline-none focus:border-cyan-500"
                >
            </div>
        </div>

        <div class="mt-6">
            <label
                for="winner_team_id"
                class="mb-2 block text-sm font-medium text-slate-200"
            >
                Winning Team
            </label>

            <select
                name="winner_team_id"
                id="winner_team_id"
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-500"
            >
                <option value="">
                    Select winner â€” leave empty for a draw
                </option>

                @if ($match->teamOne)
                    <option
                        value="{{ $match->teamOne->id }}"
                        @selected(
                            old(
                                'winner_team_id',
                                $result?->winner_team_id
                            ) == $match->teamOne->id
                        )
                    >
                        {{ $match->teamOne->name }}
                    </option>
                @endif

                @if ($match->teamTwo)
                    <option
                        value="{{ $match->teamTwo->id }}"
                        @selected(
                            old(
                                'winner_team_id',
                                $result?->winner_team_id
                            ) == $match->teamTwo->id
                        )
                    >
                        {{ $match->teamTwo->name }}
                    </option>
                @endif
            </select>
        </div>

        <div class="mt-6">
            <label
                for="remarks"
                class="mb-2 block text-sm font-medium text-slate-200"
            >
                Remarks
            </label>

            <textarea
                name="remarks"
                id="remarks"
                rows="5"
                maxlength="2000"
                placeholder="Optional notes about the match result..."
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-500"
            >{{ old('remarks', $result?->remarks) }}</textarea>
        </div>

        <div class="mt-6 rounded-lg border border-cyan-900 bg-cyan-950/30 p-4 text-sm text-cyan-200">
            The result remains pending until the tournament organizer verifies it.
        </div>

        <div class="mt-6 flex flex-wrap justify-end gap-3">
            <a
                href="{{ route('matches.show', $match) }}"
                class="rounded-lg bg-slate-700 px-5 py-2.5 font-semibold text-white hover:bg-slate-600"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="rounded-lg bg-cyan-600 px-5 py-2.5 font-semibold text-white hover:bg-cyan-500"
            >
                {{ $result ? 'Update Result' : 'Submit for Verification' }}
            </button>
        </div>
    </form>
</div>
@endsection