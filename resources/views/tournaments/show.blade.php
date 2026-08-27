@extends('layouts.app')

@section('title', $tournament->title . ' | ArenaSync')

@section('content')
    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <a
                href="{{ route('tournaments.index') }}"
                class="text-cyan-400 hover:text-cyan-300"
            >
                ← Back to tournaments
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
                <p class="font-semibold">
                    Tournament action could not be completed.
                </p>

                <ul class="mt-2 list-inside list-disc space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <article class="rounded-xl border border-slate-800 bg-slate-900 p-8">
            <div class="flex flex-wrap items-start justify-between gap-6">
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-4xl font-bold text-cyan-400">
                            {{ $tournament->title }}
                        </h1>

                        <span class="rounded-full bg-slate-800 px-3 py-1 text-sm font-semibold text-slate-300">
                            Lifecycle:
                            {{ str_replace('_', ' ', $tournament->status) }}
                        </span>

                        <span class="rounded-full bg-cyan-950 px-3 py-1 text-sm font-semibold text-cyan-300">
                            Timeline:
                            {{ $tournament->timelinePhase() }}
                        </span>
                    </div>

                    <p class="mt-4 leading-7 text-slate-300">
                        {{ $tournament->description }}
                    </p>

@auth
    <div class="mt-4">
        <a
            href="{{ route('tournaments.chat', $tournament) }}"
            class="inline-block rounded bg-cyan-600 px-4 py-2 font-semibold text-white hover:bg-cyan-500"
        >
            Tournament Chat & Announcements
        </a>
    </div>
@endauth
                </div>

                @auth
                    @if (
                        auth()->user()->hasRole(\App\Models\User::ROLE_ORGANIZER)
                        && auth()->id() === $tournament->organizer_id
                    )
                        <div class="flex flex-wrap gap-3">
                            @if ($tournament->status === \App\Models\Tournament::STATUS_DRAFT)
                                <a
                                    href="{{ route('tournaments.edit', $tournament) }}"
                                    class="rounded bg-amber-600 px-4 py-2 font-semibold text-white hover:bg-amber-500"
                                >
                                    Edit Tournament
                                </a>
                            @else
                                <a
                                    href="{{ route('tournaments.registrations.index', $tournament) }}"
                                    class="rounded bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-500"
                                >
                                    Manage Registrations
                                </a>
                            @endif
                        </div>
                    @endif
                @endauth
            </div>

            <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Game
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->game->name }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Platform
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->game->platform }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Category
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->category ?? 'Not set' }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Region
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->region ?? 'Not set' }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Prize Type
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->prize_type ?? 'Not set' }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Organizer
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->organizer->name }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Match Format
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->match_format }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Team Limit
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->team_limit }} teams
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Prize Pool
                    </p>

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
                    <p class="text-sm text-slate-500">
                        Starts
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->start_at->format('M d, Y h:i A') }}
                    </p>
                </div>

                <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Ends
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->end_at->format('M d, Y h:i A') }}
                    </p>
                </div>
            </div>

            @auth
                @if (
                    auth()->user()->hasRole(\App\Models\User::ROLE_ORGANIZER)
                    && auth()->id() === $tournament->organizer_id
                )
                    <div class="mt-8 rounded-xl border border-cyan-900 bg-slate-950 p-6">
                        <h2 class="text-xl font-semibold text-white">
                            Tournament Lifecycle Control
                        </h2>

                        <p class="mt-2 text-sm text-slate-400">
                            Current lifecycle status:
                            <span class="font-semibold text-cyan-300">
                                {{ str_replace('_', ' ', $tournament->status) }}
                            </span>
                        </p>

                        <div class="mt-5 flex flex-wrap gap-3">
                            @if ($tournament->status === \App\Models\Tournament::STATUS_DRAFT)
                                <form
                                    method="POST"
                                    action="{{ route('tournaments.publish', $tournament) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="rounded bg-cyan-600 px-4 py-2 font-semibold text-white hover:bg-cyan-500"
                                    >
                                        Publish & Open Registration
                                    </button>
                                </form>
                            @else
                                @php
                                    $nextStatuses = array_values(
                                        array_filter(
                                            $tournament->allowedStatusTransitions(),
                                            fn ($status) =>
                                                $status !== \App\Models\Tournament::STATUS_CANCELLED
                                        )
                                    );
                                @endphp

                                @foreach ($nextStatuses as $nextStatus)
                                    <form
                                        method="POST"
                                        action="{{ route('tournaments.status.update', $tournament) }}"
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
                                            class="rounded bg-cyan-600 px-4 py-2 font-semibold text-white hover:bg-cyan-500"
                                        >
                                            Move to
                                            {{ str_replace('_', ' ', $nextStatus) }}
                                        </button>
                                    </form>
                                @endforeach
                            @endif

                            @if (
                                $tournament->canTransitionTo(
                                    \App\Models\Tournament::STATUS_CANCELLED
                                )
                            )
                                <form
                                    method="POST"
                                    action="{{ route('tournaments.cancel', $tournament) }}"
                                    onsubmit="return confirm('Are you sure you want to cancel this tournament?');"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="rounded bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500"
                                    >
                                        Cancel Tournament
                                    </button>
                                </form>
                            @endif
                        </div>

                        @if (
                            $tournament->status === \App\Models\Tournament::STATUS_COMPLETED
                            || $tournament->status === \App\Models\Tournament::STATUS_CANCELLED
                        )
                            <p class="mt-4 text-sm text-slate-500">
                                This tournament has reached a terminal lifecycle state.
                                No further status transition is available.
                            </p>
                        @endif
                    </div>
                @endif
            @endauth

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
            @auth
                @if (auth()->user()->hasRole(\App\Models\User::ROLE_PLAYER))
                    <div class="mt-4 rounded-xl border border-slate-800 bg-slate-950 p-6">
                        <a href="{{ route('tournaments.payment.mine', $tournament) }}" class="text-cyan-400 hover:text-cyan-300">
                            View My Payment Status →
                        </a>
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
