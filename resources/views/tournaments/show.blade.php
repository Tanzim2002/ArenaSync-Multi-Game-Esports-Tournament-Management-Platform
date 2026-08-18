@extends('layouts.app')

@section('title', $tournament->title . ' | ArenaSync')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="mb-6">
            <a
                href="{{ route('tournaments.index') }}"
                class="text-cyan-400 hover:text-cyan-300"
            >
                ← Back to tournaments
            </a>
        </div>

        <article class="rounded-xl border border-slate-800 bg-slate-900 p-8">
            <div class="flex flex-wrap items-start justify-between gap-6">
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-4xl font-bold text-cyan-400">
                            {{ $tournament->title }}
                        </h1>

                        <span class="rounded-full bg-slate-800 px-3 py-1 text-sm font-semibold text-slate-300">
                            {{ str_replace('_', ' ', $tournament->status) }}
                        </span>
                    </div>

                    <p class="mt-4 leading-7 text-slate-300">
                        {{ $tournament->description }}
                    </p>
                </div>

                @auth
                    @if (
                        auth()->user()->hasRole(\App\Models\User::ROLE_ORGANIZER)
                        && auth()->id() === $tournament->organizer_id
                        && $tournament->status === \App\Models\Tournament::STATUS_DRAFT
                    )
                        <a
                            href="{{ route('tournaments.edit', $tournament) }}"
                            class="rounded bg-amber-600 px-4 py-2 font-semibold text-white hover:bg-amber-500"
                        >
                            Edit Tournament
                        </a>
                    @endif
                @endauth
            </div>

            <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">Game</p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->game->name }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">Organizer</p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->organizer->name }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">Match Format</p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->match_format }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">Team Limit</p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->team_limit }} teams
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">Prize Pool</p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ number_format((float) $tournament->prize_pool, 2) }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Registration Deadline
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->registration_deadline->format('M d, Y h:i A') }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">Starts</p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->start_at->format('M d, Y h:i A') }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">Ends</p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->end_at->format('M d, Y h:i A') }}
                    </p>
                </div>
            </div>

            @auth
                @if (auth()->user()->hasRole(\App\Models\User::ROLE_PLAYER))
                    <div class="mt-8 rounded-xl border border-slate-800 bg-slate-950 p-6">
                        <h2 class="text-xl font-semibold text-white">
                            Tournament Registration
                        </h2>

                        @if (
                            $tournament->status === \App\Models\Tournament::STATUS_REGISTRATION_OPEN
                            && $tournament->registration_deadline->isFuture()
                        )
                            <p class="mt-2 text-slate-400">
                                Registration is currently open. Team leaders can submit one of their teams for organizer approval.
                            </p>

                            <a
                                href="{{ route('tournaments.registrations.create', $tournament) }}"
                                class="mt-5 inline-block rounded bg-cyan-600 px-5 py-3 font-semibold text-white hover:bg-cyan-500"
                            >
                                Register Team
                            </a>
                        @elseif ($tournament->registration_deadline->isPast())
                            <p class="mt-2 text-amber-300">
                                The registration deadline for this tournament has passed.
                            </p>
                        @else
                            <p class="mt-2 text-slate-400">
                                Registration is not currently open for this tournament.
                            </p>
                        @endif
                    </div>
                @endif
            @endauth

            <div class="mt-8">
                <h2 class="text-2xl font-semibold text-white">
                    Tournament Rules
                </h2>

                <div class="mt-4 whitespace-pre-line leading-7 text-slate-300">{{ $tournament->rules }}</div>
            </div>
        </article>
    </div>
@endsection