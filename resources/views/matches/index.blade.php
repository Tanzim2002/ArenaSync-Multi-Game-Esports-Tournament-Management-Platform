@extends('layouts.app')

@section('title', 'Match Timeline | ArenaSync')

@section('content')
<div class="mx-auto max-w-6xl">
    <div class="mb-6">
        <a
            href="{{ route('tournaments.show', $tournament) }}"
            class="text-cyan-400 hover:text-cyan-300"
        >
            â† Back to Tournament
        </a>
    </div>

    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-cyan-400">
                Match Timeline
            </h1>

            <p class="mt-2 text-slate-400">
                {{ $tournament->title }}
            </p>
        </div>

        @auth
            @if (
                auth()->user()->hasRole(\App\Models\User::ROLE_ORGANIZER)
                && (int) auth()->id() === (int) $tournament->organizer_id
            )
                <div class="flex flex-wrap gap-3">
                    <a
                        href="{{ route('tournaments.matches.create', $tournament) }}"
                        class="rounded-lg bg-cyan-600 px-4 py-2 font-semibold text-white hover:bg-cyan-500"
                    >
                        Schedule Match
                    </a>

                    <a
                        href="{{ route('match-results.index', $tournament) }}"
                        class="rounded-lg bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-500"
                    >
                        Verify Results
                    </a>
                </div>
            @endif
        @endauth
    </div>

    @if ($matches->isEmpty())
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-10 text-center">
            <h2 class="text-xl font-semibold text-white">
                No matches scheduled
            </h2>

            <p class="mt-2 text-slate-400">
                Matches will appear here once the organizer schedules them.
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($matches as $match)
                <a
                    href="{{ route('matches.show', $match) }}"
                    class="block rounded-xl border border-slate-800 bg-slate-900 p-6 transition hover:border-cyan-700"
                >
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-sm text-slate-500">
                                {{ $match->round }}
                            </p>

                            <h2 class="mt-1 text-xl font-bold text-white">
                                {{ $match->teamOne->name }}
                                vs
                                {{ $match->teamTwo->name ?? 'Bye' }}
                            </h2>

                            <p class="mt-1 text-sm text-slate-400">
                                {{ $match->scheduled_at->format('M d, Y h:i A') }}
                            </p>
                        </div>

                        @if ($match->status === \App\Models\GameMatch::STATUS_COMPLETED)
                            <span class="rounded-full bg-emerald-950 px-3 py-1 text-xs font-semibold text-emerald-300">
                                COMPLETED
                            </span>
                        @elseif ($match->status === \App\Models\GameMatch::STATUS_ONGOING)
                            <span class="rounded-full bg-cyan-950 px-3 py-1 text-xs font-semibold text-cyan-300">
                                ONGOING
                            </span>
                        @elseif ($match->status === \App\Models\GameMatch::STATUS_CANCELLED)
                            <span class="rounded-full bg-red-950 px-3 py-1 text-xs font-semibold text-red-300">
                                CANCELLED
                            </span>
                        @else
                            <span class="rounded-full bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-300">
                                SCHEDULED
                            </span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection