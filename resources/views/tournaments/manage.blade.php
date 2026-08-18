@extends('layouts.app')

@section('title', 'Manage Tournaments | ArenaSync')

@section('content')
    <div class="mx-auto max-w-6xl">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-cyan-400">
                    Manage Tournaments
                </h1>

                <p class="mt-2 text-slate-400">
                    Manage your tournament drafts, classifications,
                    lifecycle statuses, and schedules.
                </p>
            </div>

            <a
                href="{{ route('tournaments.create') }}"
                class="rounded bg-cyan-600 px-5 py-3 font-semibold text-white hover:bg-cyan-500"
            >
                Create Tournament
            </a>
        </div>

        @if ($tournaments->isEmpty())
            <div class="rounded-xl border border-slate-800 bg-slate-900 p-10 text-center">
                <h2 class="text-xl font-semibold text-white">
                    No tournaments created yet
                </h2>

                <p class="mt-2 text-slate-400">
                    Create your first tournament to get started.
                </p>

                <a
                    href="{{ route('tournaments.create') }}"
                    class="mt-5 inline-block rounded bg-cyan-600 px-5 py-3 font-semibold text-white hover:bg-cyan-500"
                >
                    Create Tournament
                </a>
            </div>
        @else
            <div class="space-y-5">
                @foreach ($tournaments as $tournament)
                    <article class="rounded-xl border border-slate-800 bg-slate-900 p-6">
                        <div class="flex flex-wrap items-start justify-between gap-6">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-2xl font-bold text-cyan-400">
                                        {{ $tournament->title }}
                                    </h2>

                                    <span class="rounded-full bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-300">
                                        {{ str_replace('_', ' ', $tournament->status) }}
                                    </span>

                                    <span class="rounded-full bg-cyan-950 px-3 py-1 text-xs font-semibold text-cyan-300">
                                        {{ $tournament->timelinePhase() }}
                                    </span>
                                </div>

                                <p class="mt-2 text-sm text-slate-400">
                                    {{ $tournament->game?->name ?? 'Game unavailable' }}

                                    @if ($tournament->game?->platform)
                                        · {{ $tournament->game->platform }}
                                    @endif
                                </p>

                                <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                    <div class="rounded-lg border border-slate-800 bg-slate-950 p-3">
                                        <p class="text-xs text-slate-500">
                                            Category
                                        </p>

                                        <p class="mt-1 font-semibold text-slate-200">
                                            {{ $tournament->category ?? 'Not set' }}
                                        </p>
                                    </div>

                                    <div class="rounded-lg border border-slate-800 bg-slate-950 p-3">
                                        <p class="text-xs text-slate-500">
                                            Region
                                        </p>

                                        <p class="mt-1 font-semibold text-slate-200">
                                            {{ $tournament->region ?? 'Not set' }}
                                        </p>
                                    </div>

                                    <div class="rounded-lg border border-slate-800 bg-slate-950 p-3">
                                        <p class="text-xs text-slate-500">
                                            Prize Type
                                        </p>

                                        <p class="mt-1 font-semibold text-slate-200">
                                            {{ $tournament->prize_type ?? 'Not set' }}
                                        </p>
                                    </div>

                                    <div class="rounded-lg border border-slate-800 bg-slate-950 p-3">
                                        <p class="text-xs text-slate-500">
                                            Match Format
                                        </p>

                                        <p class="mt-1 font-semibold text-slate-200">
                                            {{ $tournament->match_format }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-5 flex flex-wrap gap-x-8 gap-y-2 text-sm text-slate-400">
                                    <p>
                                        <span class="font-semibold text-slate-300">
                                            Registration deadline:
                                        </span>
                                        {{ $tournament->registration_deadline->format('M d, Y h:i A') }}
                                    </p>

                                    <p>
                                        <span class="font-semibold text-slate-300">
                                            Starts:
                                        </span>
                                        {{ $tournament->start_at->format('M d, Y h:i A') }}
                                    </p>

                                    <p>
                                        <span class="font-semibold text-slate-300">
                                            Team limit:
                                        </span>
                                        {{ $tournament->team_limit }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <a
                                    href="{{ route('tournaments.show', $tournament) }}"
                                    class="rounded bg-cyan-600 px-4 py-2 font-semibold text-white hover:bg-cyan-500"
                                >
                                    View
                                </a>

                                @if ($tournament->status === \App\Models\Tournament::STATUS_DRAFT)
                                    <a
                                        href="{{ route('tournaments.edit', $tournament) }}"
                                        class="rounded bg-amber-600 px-4 py-2 font-semibold text-white hover:bg-amber-500"
                                    >
                                        Edit
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $tournaments->links() }}
            </div>
        @endif
    </div>
@endsection