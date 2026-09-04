@extends('layouts.app')

@section('title', $sponsor->name . ' | ArenaSync')

@section('content')
@php
    $canManageSponsor =
        auth()->check()
        &&
        (
            auth()->user()->hasRole(\App\Models\User::ROLE_ADMIN)
            ||
            (
                auth()->user()->hasRole(\App\Models\User::ROLE_ORGANIZER)
                && (int) auth()->id() === (int) $sponsor->tournament?->organizer_id
            )
        );
@endphp

<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <a
            href="{{ route('sponsors.index') }}"
            class="text-sm font-medium text-cyan-400 hover:text-cyan-300"
        >
            â† Back to Sponsors
        </a>
    </div>

    <article class="rounded-xl border border-slate-800 bg-slate-900 p-8">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
            @if ($sponsor->logo)
                <img
                    src="{{ $sponsor->logo }}"
                    alt="{{ $sponsor->name }} logo"
                    class="h-24 w-24 rounded-xl border border-slate-700 object-cover"
                >
            @else
                <div class="flex h-24 w-24 items-center justify-center rounded-xl bg-slate-950 text-3xl font-bold text-cyan-400">
                    {{ strtoupper(substr($sponsor->name, 0, 1)) }}
                </div>
            @endif

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-3xl font-bold text-cyan-400">
                        {{ $sponsor->name }}
                    </h1>

                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $sponsor->status ? 'bg-emerald-950 text-emerald-300' : 'bg-slate-800 text-slate-400' }}">
                        {{ $sponsor->status ? 'ACTIVE' : 'INACTIVE' }}
                    </span>
                </div>

                <p class="mt-2 text-slate-400">
                    {{ $sponsor->tournament?->title ?? 'Tournament unavailable' }}
                </p>

                @if ($canManageSponsor)
                    <a
                        href="{{ route('sponsors.edit', $sponsor) }}"
                        class="mt-4 inline-flex rounded-lg bg-amber-600 px-4 py-2 font-semibold text-white hover:bg-amber-500"
                    >
                        Edit Sponsor
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            @foreach ([
                ['Sponsorship Type', $sponsor->sponsorship_type ?: 'Not specified'],
                ['Amount', $sponsor->amount !== null ? number_format((float) $sponsor->amount, 2) : 'Not specified'],
                ['Contact Person', $sponsor->contact_person ?: 'Not specified'],
                ['Email', $sponsor->email ?: 'Not specified'],
                ['Phone', $sponsor->phone ?: 'Not specified'],
            ] as [$label, $value])
                <div class="rounded-lg bg-slate-950 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">
                        {{ $label }}
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $value }}
                    </p>
                </div>
            @endforeach
        </div>

        @if ($sponsor->website)
            <div class="mt-6">
                <a
                    href="{{ $sponsor->website }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-sm font-semibold text-cyan-400 hover:text-cyan-300"
                >
                    Visit Sponsor Website â†’
                </a>
            </div>
        @endif

        @if ($sponsor->description)
            <div class="mt-8 border-t border-slate-800 pt-6">
                <h2 class="text-lg font-semibold text-white">
                    About the Sponsorship
                </h2>

                <p class="mt-3 whitespace-pre-line leading-7 text-slate-300">
                    {{ $sponsor->description }}
                </p>
            </div>
        @endif
    </article>
</div>
@endsection