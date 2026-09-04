@extends('layouts.app')

@section('title', 'Leaderboard | ArenaSync')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-cyan-400">
            Leaderboard
        </h1>

        <p class="mt-2 text-slate-400">
            Rankings are calculated from verified match wins.
        </p>
    </div>

    @if ($leaderboard->isEmpty())
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-10 text-center">
            <h2 class="text-xl font-semibold text-white">
                No leaderboard data yet
            </h2>

            <p class="mt-2 text-slate-400">
                Verified match winners will appear here.
            </p>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-950">
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                            <th class="px-6 py-4">Rank</th>
                            <th class="px-6 py-4">Team</th>
                            <th class="px-6 py-4 text-right">Verified Wins</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-800">
                        @foreach ($leaderboard as $index => $entry)
                            <tr>
                                <td class="px-6 py-5">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-cyan-950 font-bold text-cyan-300">
                                        {{ $index + 1 }}
                                    </span>
                                </td>

                                <td class="px-6 py-5">
                                    @if ($entry->winnerTeam)
                                        <a
                                            href="{{ route('teams.show', $entry->winnerTeam) }}"
                                            class="font-semibold text-white hover:text-cyan-300"
                                        >
                                            {{ $entry->winnerTeam->name }}
                                        </a>
                                    @else
                                        <span class="text-slate-500">
                                            Team unavailable
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 text-right text-xl font-bold text-emerald-300">
                                    {{ $entry->wins }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection