@extends('layouts.app')

@section('title', $performance['team']->name . ' Performance | ArenaSync')

@section('content')
<div class="mx-auto max-w-6xl">
    <div class="mb-6">
        <a
            href="{{ route('teams.show', $performance['team']) }}"
            class="text-sm font-medium text-cyan-400 hover:text-cyan-300"
        >
            â† Back to Team Profile
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-cyan-400">
            {{ $performance['team']->name }} Performance
        </h1>

        <p class="mt-2 text-slate-400">
            Verified match history and team performance summary.
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

    <section class="mt-8 rounded-xl border border-slate-800 bg-slate-900 p-6">
        <h2 class="text-2xl font-semibold text-white">
            Verified Match History
        </h2>

        @if (empty($performance['history']))
            <div class="mt-5 rounded-lg bg-slate-950 p-6 text-center text-slate-400">
                No verified match history is available for this team yet.
            </div>
        @else
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                            <th class="px-3 py-3">Tournament</th>
                            <th class="px-3 py-3">Round</th>
                            <th class="px-3 py-3">Opponent</th>
                            <th class="px-3 py-3">Score</th>
                            <th class="px-3 py-3">Result</th>
                            <th class="px-3 py-3">Date</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-800">
                        @foreach ($performance['history'] as $match)
                            <tr>
                                <td class="px-3 py-4 font-medium text-white">
                                    {{ $match['tournament'] }}
                                </td>

                                <td class="px-3 py-4 text-slate-300">
                                    {{ $match['round'] }}
                                </td>

                                <td class="px-3 py-4 text-slate-300">
                                    {{ $match['opponent'] }}
                                </td>

                                <td class="px-3 py-4 font-semibold text-slate-200">
                                    {{ $match['team_score'] }} - {{ $match['opponent_score'] }}
                                </td>

                                <td class="px-3 py-4">
                                    <span class="rounded-full bg-slate-800 px-3 py-1 text-xs font-semibold text-cyan-300">
                                        {{ $match['outcome'] }}
                                    </span>
                                </td>

                                <td class="px-3 py-4 text-slate-400">
                                    {{ $match['scheduled_at']
                                        ? $match['scheduled_at']->format('M d, Y h:i A')
                                        : 'Not available' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection