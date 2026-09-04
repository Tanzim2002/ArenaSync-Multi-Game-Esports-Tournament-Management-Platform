@extends('layouts.app')

@section('title', $team->name . ' | ArenaSync')

@section('content')
@php
    $isLeader =
        (int) $team->leader_id
        ===
        (int) auth()->id();

    $currentMember =
        $team->teamMembers
            ->firstWhere(
                'user_id',
                auth()->id()
            );

    $isMember =
        $currentMember !== null;

    $myPendingRequest =
        $pendingRequests
            ->firstWhere(
                'user_id',
                auth()->id()
            );
@endphp

<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a
            href="{{ route('tournaments.index') }}"
            class="text-sm font-medium text-cyan-400 transition hover:text-cyan-300"
        >
            ← Back to Tournaments
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-emerald-700 bg-emerald-950/50 p-4 text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
            <ul class="list-inside list-disc space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <article class="rounded-xl border border-slate-800 bg-slate-900 p-6 shadow-xl sm:p-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
            @if ($team->logo)
                <img
                    src="{{ asset('storage/' . $team->logo) }}"
                    alt="{{ $team->name }} logo"
                    class="h-32 w-32 rounded-xl border border-slate-700 object-cover"
                >
            @else
                <div class="flex h-32 w-32 items-center justify-center rounded-xl border border-slate-700 bg-slate-950 text-4xl font-bold text-cyan-400">
                    {{ strtoupper(substr($team->name, 0, 1)) }}
                </div>
            @endif

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-cyan-400">
                            {{ $team->name }}
                        </h1>

                        <p class="mt-2 text-slate-400">
                            Led by
                            <span class="font-semibold text-slate-200">
                                {{ $team->leader->name }}
                            </span>
                        </p>
                    </div>

                    @if ($isLeader)
                        <a
                            href="{{ route('teams.edit', $team) }}"
                            class="rounded-lg bg-amber-600 px-4 py-2 font-semibold text-white transition hover:bg-amber-500"
                        >
                            Edit Team
                        </a>
                    @endif
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-lg bg-slate-950 p-4">
                        <p class="text-sm text-slate-500">
                            Preferred Game
                        </p>

                        <p class="mt-1 font-semibold text-slate-200">
                            {{ $team->preferredGame?->name ?? 'Not selected' }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-slate-950 p-4">
                        <p class="text-sm text-slate-500">
                            Members
                        </p>

                        <p class="mt-1 font-semibold text-slate-200">
                            {{ $team->teamMembers->count() }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-slate-950 p-4">
                        <p class="text-sm text-slate-500">
                            Created
                        </p>

                        <p class="mt-1 font-semibold text-slate-200">
                            {{ $team->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>

                <div class="mt-5">
                    <p class="text-sm text-slate-500">
                        Description
                    </p>

                    <p class="mt-2 leading-7 text-slate-300">
                        {{ $team->description ?? 'No description added.' }}
                    </p>
                </div>

                <div class="mt-5">
                    <a
                        href="{{ route('performance.teams.show', $team) }}"
                        class="inline-flex rounded-lg bg-violet-600 px-4 py-2 font-semibold text-white transition hover:bg-violet-500"
                    >
                        View Team Performance
                    </a>
                </div>
            </div>
        </div>
    </article>

    <section class="mt-8 rounded-xl border border-slate-800 bg-slate-900 p-6">
        <h2 class="text-2xl font-semibold text-white">
            Team Members
        </h2>

        @if ($team->teamMembers->isEmpty())
            <div class="mt-5 rounded-lg bg-slate-950 p-5 text-slate-400">
                No players have joined this team yet.
            </div>
        @else
            <div class="mt-5 space-y-4">
                @foreach ($team->teamMembers as $member)
                    <div class="rounded-lg border border-slate-800 bg-slate-950 p-5">
                        <div class="flex flex-col justify-between gap-4 lg:flex-row">
                            <div>
                                <p class="font-semibold text-white">
                                    {{ $member->user->name }}
                                </p>

                                <p class="text-sm text-slate-400">
                                    {{ $member->user->email }}
                                </p>

                                <span class="mt-2 inline-flex rounded-full bg-cyan-950 px-3 py-1 text-xs font-semibold text-cyan-300">
                                    {{ $member->role }}
                                </span>

                                <div class="mt-3">
                                    <a
                                        href="{{ route('performance.players.show', $member->user) }}"
                                        class="text-sm font-medium text-violet-400 hover:text-violet-300"
                                    >
                                        View Player Performance
                                    </a>
                                </div>
                            </div>

                            @if ($isLeader)
                                <form
                                    method="POST"
                                    action="{{ route('teams.members.role.update', [$team, $member]) }}"
                                    class="flex flex-wrap items-end gap-3"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <div>
                                        <label
                                            for="role-{{ $member->id }}"
                                            class="mb-2 block text-xs font-medium text-slate-400"
                                        >
                                            Team Role
                                        </label>

                                        <select
                                            id="role-{{ $member->id }}"
                                            name="role"
                                            required
                                            class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-white"
                                        >
                                            <option
                                                value="CAPTAIN"
                                                @selected($member->role === 'CAPTAIN')
                                            >
                                                Captain
                                            </option>

                                            <option
                                                value="MEMBER"
                                                @selected($member->role === 'MEMBER')
                                            >
                                                Member
                                            </option>

                                            <option
                                                value="SUBSTITUTE"
                                                @selected($member->role === 'SUBSTITUTE')
                                            >
                                                Substitute
                                            </option>
                                        </select>
                                    </div>

                                    <button
                                        type="submit"
                                        class="rounded-lg bg-cyan-600 px-4 py-2 font-semibold text-white transition hover:bg-cyan-500"
                                    >
                                        Update Role
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    @if ($isLeader)
        <section class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-800 bg-slate-900 p-6">
                <h2 class="text-xl font-semibold text-white">
                    Invite Player
                </h2>

                <p class="mt-2 text-sm text-slate-400">
                    Send an invitation using the player's account email.
                </p>

                <form
                    method="POST"
                    action="{{ route('teams.invitations.store', $team) }}"
                    class="mt-5 space-y-4"
                >
                    @csrf

                    <div>
                        <label
                            for="email"
                            class="mb-2 block text-sm font-medium text-slate-200"
                        >
                            Player Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none transition focus:border-cyan-500"
                        >
                    </div>

                    <button
                        type="submit"
                        class="rounded-lg bg-cyan-600 px-5 py-2.5 font-semibold text-white transition hover:bg-cyan-500"
                    >
                        Send Invitation
                    </button>
                </form>
            </div>

            <div class="rounded-xl border border-slate-800 bg-slate-900 p-6">
                <h2 class="text-xl font-semibold text-white">
                    Pending Membership Requests
                </h2>

                @if ($pendingRequests->isEmpty())
                    <p class="mt-5 text-slate-400">
                        No pending requests.
                    </p>
                @else
                    <div class="mt-5 space-y-4">
                        @foreach ($pendingRequests as $membershipRequest)
                            <div class="rounded-lg bg-slate-950 p-4">
                                <p class="font-semibold text-white">
                                    {{ $membershipRequest->user->name }}
                                </p>

                                <p class="text-sm text-slate-400">
                                    {{ $membershipRequest->user->email }}
                                </p>

                                <p class="mt-2 text-xs font-semibold text-cyan-300">
                                    {{ str_replace('_', ' ', $membershipRequest->request_type) }}
                                </p>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    @if ($membershipRequest->request_type === 'JOIN_REQUEST')
                                        <form
                                            method="POST"
                                            action="{{ route('team-membership-requests.accept', $membershipRequest) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="rounded-lg bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-500"
                                            >
                                                Accept
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('team-membership-requests.reject', $membershipRequest) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500"
                                            >
                                                Reject
                                            </button>
                                        </form>
                                    @else
                                        <form
                                            method="POST"
                                            action="{{ route('team-membership-requests.cancel', $membershipRequest) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="rounded-lg bg-slate-700 px-4 py-2 font-semibold text-white hover:bg-slate-600"
                                            >
                                                Cancel Invitation
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @else
        <section class="mt-8 rounded-xl border border-slate-800 bg-slate-900 p-6">
            @if ($isMember)
                <h2 class="text-xl font-semibold text-white">
                    Your Membership
                </h2>

                <p class="mt-2 text-slate-400">
                    You are currently a member of this team.
                </p>

                <p class="mt-3 font-semibold text-cyan-300">
                    Role: {{ $currentMember->role }}
                </p>

                <form
                    method="POST"
                    action="{{ route('teams.leave', $team) }}"
                    class="mt-5"
                    onsubmit="return confirm('Are you sure you want to leave this team?');"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="rounded-lg bg-red-600 px-5 py-2.5 font-semibold text-white hover:bg-red-500"
                    >
                        Leave Team
                    </button>
                </form>

            @elseif ($myPendingRequest)
                @if ($myPendingRequest->request_type === 'INVITATION')
                    <h2 class="text-xl font-semibold text-white">
                        Team Invitation
                    </h2>

                    <p class="mt-2 text-slate-400">
                        You have been invited to join this team.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <form
                            method="POST"
                            action="{{ route('team-membership-requests.accept', $myPendingRequest) }}"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="rounded-lg bg-emerald-600 px-5 py-2.5 font-semibold text-white hover:bg-emerald-500"
                            >
                                Accept Invitation
                            </button>
                        </form>

                        <form
                            method="POST"
                            action="{{ route('team-membership-requests.reject', $myPendingRequest) }}"
                        >
                            @csrf
                            @method('PATCH')

                            <button
                                type="submit"
                                class="rounded-lg bg-red-600 px-5 py-2.5 font-semibold text-white hover:bg-red-500"
                            >
                                Reject Invitation
                            </button>
                        </form>
                    </div>
                @else
                    <h2 class="text-xl font-semibold text-white">
                        Join Request
                    </h2>

                    <p class="mt-2 text-slate-400">
                        Your request to join this team is pending.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('team-membership-requests.cancel', $myPendingRequest) }}"
                        class="mt-5"
                    >
                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="rounded-lg bg-slate-700 px-5 py-2.5 font-semibold text-white hover:bg-slate-600"
                        >
                            Cancel Join Request
                        </button>
                    </form>
                @endif

            @else
                <h2 class="text-xl font-semibold text-white">
                    Join Team
                </h2>

                <p class="mt-2 text-slate-400">
                    Request membership in this team.
                </p>

                <form
                    method="POST"
                    action="{{ route('teams.join-requests.store', $team) }}"
                    class="mt-5"
                >
                    @csrf

                    <button
                        type="submit"
                        class="rounded-lg bg-cyan-600 px-5 py-2.5 font-semibold text-white hover:bg-cyan-500"
                    >
                        Request to Join
                    </button>
                </form>
            @endif
        </section>
    @endif

    <div class="mt-8">
        <a
            href="{{ route('teams.create') }}"
            class="inline-flex rounded-lg bg-slate-700 px-5 py-2.5 font-semibold text-white transition hover:bg-slate-600"
        >
            Create Another Team
        </a>
    </div>
</div>
@endsection