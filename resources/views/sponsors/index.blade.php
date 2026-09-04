@extends('layouts.app')

@section('title', 'Sponsors | ArenaSync')

@section('content')
@php
    $canManageSponsors =
        auth()->check()
        && auth()->user()->hasRole(
            \App\Models\User::ROLE_ORGANIZER,
            \App\Models\User::ROLE_ADMIN
        );
@endphp

<div class="mx-auto max-w-6xl">
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-cyan-400">
                Sponsors
            </h1>

            <p class="mt-2 text-slate-400">
                Tournament partners and sponsorship records.
            </p>
        </div>

        @if ($canManageSponsors)
            <a
                href="{{ route('sponsors.create') }}"
                class="rounded-lg bg-cyan-600 px-5 py-3 font-semibold text-white hover:bg-cyan-500"
            >
                Add Sponsor
            </a>
        @endif
    </div>

    @if ($sponsors->isEmpty())
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-10 text-center">
            <h2 class="text-xl font-semibold text-white">
                No sponsors added yet
            </h2>

            <p class="mt-2 text-slate-400">
                Tournament sponsor records will appear here.
            </p>
        </div>
    @else
        <div class="grid gap-5 md:grid-cols-2">
            @foreach ($sponsors as $sponsor)
                <article class="rounded-xl border border-slate-800 bg-slate-900 p-6">
                    <div class="flex items-start gap-4">
                        @if ($sponsor->logo)
                            <img
                                src="{{ $sponsor->logo }}"
                                alt="{{ $sponsor->name }} logo"
                                class="h-14 w-14 rounded-lg border border-slate-700 object-cover"
                            >
                        @else
                            <div class="flex h-14 w-14 items-center justify-center rounded-lg bg-slate-950 text-xl font-bold text-cyan-400">
                                {{ strtoupper(substr($sponsor->name, 0, 1)) }}
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-xl font-bold text-white">
                                    {{ $sponsor->name }}
                                </h2>

                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $sponsor->status ? 'bg-emerald-950 text-emerald-300' : 'bg-slate-800 text-slate-400' }}">
                                    {{ $sponsor->status ? 'ACTIVE' : 'INACTIVE' }}
                                </span>
                            </div>

                            <p class="mt-1 text-sm text-slate-400">
                                {{ $sponsor->tournament?->title ?? 'Tournament unavailable' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="rounded-lg bg-slate-950 p-3">
                            <p class="text-xs text-slate-500">
                                Sponsorship Type
                            </p>

                            <p class="mt-1 font-semibold text-slate-200">
                                {{ $sponsor->sponsorship_type ?: 'Not specified' }}
                            </p>
                        </div>

                        <div class="rounded-lg bg-slate-950 p-3">
                            <p class="text-xs text-slate-500">
                                Amount
                            </p>

                            <p class="mt-1 font-semibold text-slate-200">
                                {{ $sponsor->amount !== null ? number_format((float) $sponsor->amount, 2) : 'Not specified' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a
                            href="{{ route('sponsors.show', $sponsor) }}"
                            class="rounded-lg bg-slate-700 px-4 py-2 font-semibold text-white hover:bg-slate-600"
                        >
                            View
                        </a>

                        @if ($canManageSponsors)
                            <a
                                href="{{ route('sponsors.edit', $sponsor) }}"
                                class="rounded-lg bg-amber-600 px-4 py-2 font-semibold text-white hover:bg-amber-500"
                            >
                                Edit
                            </a>

                            <form
                                method="POST"
                                action="{{ route('sponsors.destroy', $sponsor) }}"
                                onsubmit="return confirm('Delete this sponsor?');"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500"
                                >
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $sponsors->links() }}
        </div>
    @endif
</div>
@endsection