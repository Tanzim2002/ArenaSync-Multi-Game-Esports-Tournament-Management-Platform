@extends('layouts.app')

@section('title', $match->teamOne->name . ' vs ' . ($match->teamTwo->name ?? 'Bye') . ' | ArenaSync')

@section('content')
@php
    $currentUserId = auth()->id();

    $isParticipant =
        auth()->check()
        && auth()->user()->hasRole(\App\Models\User::ROLE_PLAYER)
        && (
            (int) $match->teamOne->leader_id === (int) $currentUserId
            || $match->teamOne->teamMembers()->where('user_id', $currentUserId)->exists()
            || (
                $match->teamTwo
                && (
                    (int) $match->teamTwo->leader_id === (int) $currentUserId
                    || $match->teamTwo->teamMembers()->where('user_id', $currentUserId)->exists()
                )
            )
        );

    $isOwnerOrganizer =
        auth()->check()
        && auth()->user()->hasRole(\App\Models\User::ROLE_ORGANIZER)
        && (int) auth()->id() === (int) $match->tournament->organizer_id;

    $result = $match->result;
@endphp

<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <a
            href="{{ route('tournaments.matches.index', $match->tournament) }}"
            class="text-cyan-400 hover:text-cyan-300"
        >
            â† Back to Match Timeline
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    <article class="rounded-xl border border-slate-800 bg-slate-900 p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500">
                    {{ $match->tournament->title }} Â· {{ $match->round }}
                </p>

                <h1 class="mt-1 text-3xl font-bold text-white">
                    {{ $match->teamOne->name }}
                    vs
                    {{ $match->teamTwo->name ?? 'Bye' }}
                </h1>

                <p class="mt-3 text-slate-400">
                    {{ $match->scheduled_at->format('M d, Y h:i A') }}
                </p>
            </div>

            <span class="rounded-full bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-300">
                {{ $match->status }}
            </span>
        </div>

        @if ($result)
            <div class="mt-8 rounded-xl border border-slate-800 bg-slate-950 p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-slate-500">
                            Submitted Result
                        </p>

                        <p class="mt-2 text-3xl font-black text-white">
                            {{ $result->team_one_score }}
                            <span class="text-slate-600">:</span>
                            {{ $result->team_two_score }}
                        </p>
                    </div>

                    @if ($result->isVerified())
                        <span class="rounded-full bg-emerald-950 px-3 py-1 text-xs font-semibold text-emerald-300">
                            VERIFIED
                        </span>
                    @elseif ($result->isRejected())
                        <span class="rounded-full bg-red-950 px-3 py-1 text-xs font-semibold text-red-300">
                            REJECTED
                        </span>
                    @else
                        <span class="rounded-full bg-amber-950 px-3 py-1 text-xs font-semibold text-amber-300">
                            PENDING
                        </span>
                    @endif
                </div>

                @if ($result->winnerTeam)
                    <p class="mt-3 text-sm text-slate-300">
                        Winner:
                        <span class="font-semibold text-emerald-300">
                            {{ $result->winnerTeam->name }}
                        </span>
                    </p>
                @elseif ($result->team_one_score === $result->team_two_score)
                    <p class="mt-3 text-sm font-semibold text-slate-300">
                        Draw
                    </p>
                @endif

                @if ($result->review_notes)
                    <p class="mt-3 text-sm text-red-300">
                        Organizer note: {{ $result->review_notes }}
                    </p>
                @endif
            </div>
        @endif

        <div class="mt-8 flex flex-wrap gap-3">
            @if (
                $isParticipant
                && $match->status !== \App\Models\GameMatch::STATUS_CANCELLED
                && (! $result || ! $result->isVerified())
            )
                <a
                    href="{{ route('match-results.create', $match) }}"
                    class="rounded-lg bg-violet-600 px-4 py-2 font-semibold text-white hover:bg-violet-500"
                >
                    {{ $result ? 'Update Result' : 'Submit Result' }}
                </a>
            @endif

            @if ($isOwnerOrganizer)
                <a
                    href="{{ route('match-results.index', $match->tournament) }}"
                    class="rounded-lg bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-500"
                >
                    Verify Results
                </a>

                @foreach ($match->allowedStatusTransitions() as $nextStatus)
                    <form
                        method="POST"
                        action="{{ route('matches.status.update', $match) }}"
                    >
                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="status"
                            value="{{ $nextStatus }}"
                        >

                        <button
                            type="submit"
                            class="rounded-lg bg-cyan-600 px-4 py-2 font-semibold text-white hover:bg-cyan-500"
                        >
                            Move to {{ str_replace('_', ' ', $nextStatus) }}
                        </button>
                    </form>
                @endforeach
            @endif
        </div>
    </article>
</div>
@endsection