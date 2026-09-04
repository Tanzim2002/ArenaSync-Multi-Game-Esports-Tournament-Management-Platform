@extends('layouts.app')

@section('title', $performance['player']->name . ' Performance | ArenaSync')

@section('content')
<div class="mx-auto max-w-6xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-cyan-400">
            {{ $performance['player']->name }} Performance
        </h1>

        <p class="mt-2 max-w-3xl text-slate-400">
            Aggregated performance from verified match results for teams connected
            to this player.
        </p>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
        @foreach ([
            ['Matches', $performance['summary']['matches_played']],
            ['Wins', $performance['summary']['wins']],
            ['Losses', $performance['summary']['losses']],
            ['Draws', $performance['summary']['draws']],
            ['Score For', $performance['summary']['score_for']],
            ['Score Against', $performance['summary']['score_against']],
            ['Win Rate', $performance['summary']['win_rate'] . '%'],
        ] as [$label, $value])
            <div class="rounded-xl border border-slate-800 bg-slate-900 p-4">
                <p class="text-xs uppercase tracking-wide text-slate-500">
                    {{ $label }}
                </p>

                <p class="mt-2 text-2xl font-bold text-white">
                    {{ $value }}
                </p>
            </div>
        @endforeach
    </section>

    <section class="mt-8">
        <h2 class="text-2xl font-semibold text-white">
            Team Performance
        </h2>

        @if ($performance['team_performances']->isEmpty())
            <div class="mt-5 rounded-xl border border-slate-800 bg-slate-900 p-8 text-center text-slate-400">
                No team performance history is available for this player yet.
            </div>
        @else
            <div class="mt-5 grid gap-5 md:grid-cols-2">
                @foreach ($performance['team_performances'] as $teamPerformance)
                    <article class="rounded-xl border border-slate-800 bg-slate-900 p-6">
                        <h3 class="text-xl font-bold text-white">
                            {{ $teamPerformance['team']->name }}
                        </h3>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-lg bg-slate-950 p-3">
                                <p class="text-xs text-slate-500">Matches</p>
                                <p class="mt-1 font-bold text-white">
                                    {{ $teamPerformance['summary']['matches_played'] }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-slate-950 p-3">
                                <p class="text-xs text-slate-500">Wins</p>
                                <p class="mt-1 font-bold text-emerald-300">
                                    {{ $teamPerformance['summary']['wins'] }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-slate-950 p-3">
                                <p class="text-xs text-slate-500">Losses</p>
                                <p class="mt-1 font-bold text-red-300">
                                    {{ $teamPerformance['summary']['losses'] }}
                                </p>
                            </div>

                            <div class="rounded-lg bg-slate-950 p-3">
                                <p class="text-xs text-slate-500">Win Rate</p>
                                <p class="mt-1 font-bold text-cyan-300">
                                    {{ $teamPerformance['summary']['win_rate'] }}%
                                </p>
                            </div>
                        </div>

                        <a
                            href="{{ route('performance.teams.show', $teamPerformance['team']) }}"
                            class="mt-5 inline-flex text-sm font-semibold text-cyan-400 hover:text-cyan-300"
                        >
                            View Team Performance â†’
                        </a>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection