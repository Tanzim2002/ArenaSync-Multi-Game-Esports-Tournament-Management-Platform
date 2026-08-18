@extends('layouts.app')

@section('title', 'Participant Approvals | ArenaSync')

@section('content')
    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <a
                href="{{ route('tournaments.show', $tournament) }}"
                class="text-cyan-400 hover:text-cyan-300"
            >
                ← Back to tournament
            </a>
        </div>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-cyan-400">
                Participant Approvals
            </h1>

            <p class="mt-2 text-slate-400">
                Review team registrations submitted for
                <span class="font-semibold text-slate-200">
                    {{ $tournament->title }}
                </span>.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
                <p class="font-semibold">
                    Registration action could not be completed.
                </p>

                <ul class="mt-2 list-inside list-disc space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-8 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-amber-800 bg-slate-900 p-5">
                <p class="text-sm text-slate-400">
                    Pending
                </p>

                <p class="mt-2 text-3xl font-bold text-amber-300">
                    {{ $pendingCount }}
                </p>
            </div>

            <div class="rounded-xl border border-emerald-800 bg-slate-900 p-5">
                <p class="text-sm text-slate-400">
                    Approved
                </p>

                <p class="mt-2 text-3xl font-bold text-emerald-300">
                    {{ $approvedCount }}
                </p>
            </div>

            <div class="rounded-xl border border-red-800 bg-slate-900 p-5">
                <p class="text-sm text-slate-400">
                    Rejected
                </p>

                <p class="mt-2 text-3xl font-bold text-red-300">
                    {{ $rejectedCount }}
                </p>
            </div>
        </div>

        <div class="mb-6 rounded-xl border border-slate-800 bg-slate-900 p-5">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <p class="text-xs text-slate-500">
                        Tournament
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->title }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500">
                        Game
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->game->name }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500">
                        Lifecycle Status
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ str_replace('_', ' ', $tournament->status) }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500">
                        Team Limit
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $tournament->team_limit }}
                    </p>
                </div>
            </div>
        </div>

        @if ($registrations->isEmpty())
            <div class="rounded-xl border border-slate-800 bg-slate-900 p-10 text-center">
                <h2 class="text-xl font-semibold text-white">
                    No registrations submitted
                </h2>

                <p class="mt-2 text-slate-400">
                    Teams have not submitted any registrations for this tournament yet.
                </p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($registrations as $registration)
                    <article class="rounded-xl border border-slate-800 bg-slate-900 p-6">
                        <div class="flex flex-wrap items-start justify-between gap-6">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-xl font-bold text-white">
                                        {{ $registration->team->name }}
                                    </h2>

                                    @if ($registration->status === \App\Models\Registration::STATUS_PENDING)
                                        <span class="rounded-full bg-amber-950 px-3 py-1 text-xs font-semibold text-amber-300">
                                            PENDING
                                        </span>
                                    @elseif ($registration->status === \App\Models\Registration::STATUS_APPROVED)
                                        <span class="rounded-full bg-emerald-950 px-3 py-1 text-xs font-semibold text-emerald-300">
                                            APPROVED
                                        </span>
                                    @elseif ($registration->status === \App\Models\Registration::STATUS_REJECTED)
                                        <span class="rounded-full bg-red-950 px-3 py-1 text-xs font-semibold text-red-300">
                                            REJECTED
                                        </span>
                                    @else
                                        <span class="rounded-full bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-300">
                                            {{ $registration->status }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-4 grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-3">
                                    <div>
                                        <p class="text-slate-500">
                                            Team Leader
                                        </p>

                                        <p class="mt-1 font-semibold text-slate-200">
                                            {{ $registration->team->leader->name }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-slate-500">
                                            Submitted
                                        </p>

                                        <p class="mt-1 font-semibold text-slate-200">
                                            {{ $registration->submitted_at?->format('M d, Y h:i A') ?? 'Not available' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-slate-500">
                                            Registration ID
                                        </p>

                                        <p class="mt-1 font-semibold text-slate-200">
                                            #{{ $registration->id }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if ($registration->isReviewable())
                                <div class="flex flex-wrap gap-3">
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'tournaments.registrations.approve',
                                            [
                                                $tournament,
                                                $registration,
                                            ]
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="rounded bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-500"
                                        >
                                            Approve
                                        </button>
                                    </form>

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'tournaments.registrations.reject',
                                            [
                                                $tournament,
                                                $registration,
                                            ]
                                        ) }}"
                                        onsubmit="return confirm('Reject this team registration?');"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="rounded bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500"
                                        >
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="text-sm text-slate-500">
                                    Decision completed
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
@endsection