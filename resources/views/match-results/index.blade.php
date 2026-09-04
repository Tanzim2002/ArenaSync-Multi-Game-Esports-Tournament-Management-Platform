@extends('layouts.app')

@section('title', 'Match Result Verification | ArenaSync')

@section('content')
<div class="mx-auto max-w-6xl">
    <div class="mb-6">
        <a
            href="{{ route('tournaments.show', $tournament) }}"
            class="text-sm font-medium text-cyan-400 hover:text-cyan-300"
        >
            â† Back to Tournament
        </a>
    </div>

    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-cyan-400">
                Match Result Verification
            </h1>

            <p class="mt-2 text-slate-400">
                Review submissions for
                <span class="font-semibold text-slate-200">
                    {{ $tournament->title }}
                </span>.
            </p>
        </div>

        <span class="rounded-full bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-300">
            {{ $results->count() }}
            {{ $results->count() === 1 ? 'submission' : 'submissions' }}
        </span>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    @if ($results->isEmpty())
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-10 text-center">
            <h2 class="text-xl font-semibold text-white">
                No result submissions yet
            </h2>

            <p class="mt-2 text-slate-400">
                Participant submissions will appear here for organizer review.
            </p>
        </div>
    @else
        <div class="space-y-5">
            @foreach ($results as $result)
                @php
                    $match = $result->match;
                @endphp

                <article class="rounded-xl border border-slate-800 bg-slate-900 p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-white">
                                {{ $match?->teamOne?->name ?? 'Team One' }}
                                vs
                                {{ $match?->teamTwo?->name ?? 'Team Two' }}
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Match #{{ $match?->id ?? '-' }}
                                @if ($match?->round)
                                    Â· {{ $match->round }}
                                @endif
                            </p>
                        </div>

                        @if ($result->isPending())
                            <span class="rounded-full bg-amber-950 px-3 py-1 text-xs font-semibold text-amber-300">
                                PENDING
                            </span>
                        @elseif ($result->isVerified())
                            <span class="rounded-full bg-emerald-950 px-3 py-1 text-xs font-semibold text-emerald-300">
                                VERIFIED
                            </span>
                        @else
                            <span class="rounded-full bg-red-950 px-3 py-1 text-xs font-semibold text-red-300">
                                REJECTED
                            </span>
                        @endif
                    </div>

                    <div class="mt-6 grid items-center gap-4 sm:grid-cols-[1fr_auto_1fr]">
                        <div class="rounded-lg bg-slate-950 p-5 text-center">
                            <p class="font-semibold text-slate-300">
                                {{ $match?->teamOne?->name ?? 'Team One' }}
                            </p>

                            <p class="mt-2 text-4xl font-black text-white">
                                {{ $result->team_one_score }}
                            </p>

                            @if (
                                $result->winner_team_id
                                && $result->winner_team_id === $match?->team_one_id
                            )
                                <p class="mt-2 text-xs font-semibold text-emerald-300">
                                    WINNER
                                </p>
                            @endif
                        </div>

                        <div class="font-bold text-slate-600">
                            VS
                        </div>

                        <div class="rounded-lg bg-slate-950 p-5 text-center">
                            <p class="font-semibold text-slate-300">
                                {{ $match?->teamTwo?->name ?? 'Team Two' }}
                            </p>

                            <p class="mt-2 text-4xl font-black text-white">
                                {{ $result->team_two_score }}
                            </p>

                            @if (
                                $result->winner_team_id
                                && $result->winner_team_id === $match?->team_two_id
                            )
                                <p class="mt-2 text-xs font-semibold text-emerald-300">
                                    WINNER
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-5 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-lg bg-slate-950 p-4">
                            <p class="text-xs uppercase text-slate-500">Submitted By</p>
                            <p class="mt-1 font-semibold text-slate-200">
                                {{ $result->submittedBy?->name ?? 'Unknown User' }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-slate-950 p-4">
                            <p class="text-xs uppercase text-slate-500">Submitted At</p>
                            <p class="mt-1 font-semibold text-slate-200">
                                {{ $result->submitted_at?->format('M d, Y h:i A') ?? 'â€”' }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-slate-950 p-4">
                            <p class="text-xs uppercase text-slate-500">Winner</p>
                            <p class="mt-1 font-semibold text-slate-200">
                                {{ $result->winnerTeam?->name ?? 'Draw' }}
                            </p>
                        </div>
                    </div>

                    @if ($result->remarks)
                        <div class="mt-5 rounded-lg border border-slate-800 bg-slate-950 p-4">
                            <p class="text-xs font-semibold uppercase text-slate-500">
                                Participant Remarks
                            </p>

                            <p class="mt-2 text-sm leading-6 text-slate-300">
                                {{ $result->remarks }}
                            </p>
                        </div>
                    @endif

                    @if ($result->isPending())
                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            <form
                                method="POST"
                                action="{{ route('match-results.verify', $result) }}"
                                class="rounded-lg border border-emerald-900 bg-emerald-950/20 p-4"
                            >
                                @csrf
                                @method('PATCH')

                                <p class="text-sm text-slate-400">
                                    Confirm the submitted score and winner.
                                </p>

                                <button
                                    type="submit"
                                    class="mt-4 rounded-lg bg-emerald-600 px-5 py-2.5 font-semibold text-white hover:bg-emerald-500"
                                >
                                    Verify Result
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('match-results.reject', $result) }}"
                                class="rounded-lg border border-red-900 bg-red-950/20 p-4"
                            >
                                @csrf
                                @method('PATCH')

                                <label class="block text-sm font-medium text-slate-200">
                                    Correction Note
                                </label>

                                <textarea
                                    name="review_notes"
                                    rows="3"
                                    maxlength="2000"
                                    required
                                    placeholder="Explain what needs to be corrected..."
                                    class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-white outline-none focus:border-red-500"
                                ></textarea>

                                <button
                                    type="submit"
                                    class="mt-3 rounded-lg bg-red-600 px-5 py-2.5 font-semibold text-white hover:bg-red-500"
                                >
                                    Reject Result
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mt-6 rounded-lg border border-slate-800 bg-slate-950 p-4 text-sm text-slate-300">
                            @if ($result->isVerified())
                                This result has been verified.
                            @else
                                <span class="font-semibold text-red-300">
                                    Result rejected.
                                </span>

                                @if ($result->review_notes)
                                    <span class="ml-1">
                                        {{ $result->review_notes }}
                                    </span>
                                @endif
                            @endif

                            @if ($result->reviewed_at)
                                <span class="mt-1 block text-slate-500">
                                    Reviewed {{ $result->reviewed_at->format('M d, Y h:i A') }}
                                </span>
                            @endif
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection