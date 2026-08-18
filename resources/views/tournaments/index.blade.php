@extends('layouts.app')

@section('title', 'Tournaments | ArenaSync')

@section('content')
    <div class="mx-auto max-w-6xl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-cyan-400">
                Tournaments
            </h1>

            <p class="mt-2 text-slate-400">
                Browse available ArenaSync tournaments.
            </p>
        </div>

        @if ($tournaments->isEmpty())
            <div class="rounded-xl border border-slate-800 bg-slate-900 p-10 text-center">
                <h2 class="text-xl font-semibold text-white">
                    No tournaments available
                </h2>

                <p class="mt-2 text-slate-400">
                    There are currently no public tournaments to display.
                </p>
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($tournaments as $tournament)
                    <article class="rounded-xl border border-slate-800 bg-slate-900 p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-cyan-400">
                                    {{ $tournament->title }}
                                </h2>

                                <p class="mt-1 text-sm text-slate-400">
                                    {{ $tournament->game?->name ?? 'Game unavailable' }}
                                </p>
                            </div>

                            <span class="rounded-full bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-300">
                                {{ str_replace('_', ' ', $tournament->status) }}
                            </span>
                        </div>

                        <div class="mt-5 space-y-3 text-sm text-slate-300">
                            <div>
                                <span class="font-semibold text-slate-400">
                                    Registration deadline:
                                </span>

                                <div class="mt-1">
                                    {{ $tournament->registration_deadline->format('M d, Y h:i A') }}
                                </div>
                            </div>

                            <div>
                                <span class="font-semibold text-slate-400">
                                    Starts:
                                </span>

                                <div class="mt-1">
                                    {{ $tournament->start_at->format('M d, Y h:i A') }}
                                </div>
                            </div>

                            <div>
                                <span class="font-semibold text-slate-400">
                                    Team limit:
                                </span>

                                <div class="mt-1">
                                    {{ $tournament->team_limit }}
                                </div>
                            </div>
                        </div>

                        <a
                            href="{{ route('tournaments.show', $tournament) }}"
                            class="mt-6 inline-block rounded bg-cyan-600 px-4 py-2 font-semibold text-white hover:bg-cyan-500"
                        >
                            View Tournament
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $tournaments->links() }}
            </div>
        @endif
    </div>
@endsection