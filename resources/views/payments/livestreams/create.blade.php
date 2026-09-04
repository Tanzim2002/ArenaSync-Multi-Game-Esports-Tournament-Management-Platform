@extends('layouts.app')

@section('title', 'Add Livestream | ArenaSync')

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <a
            href="{{ route('tournaments.livestreams.index', $tournament) }}"
            class="text-sm font-medium text-cyan-400 hover:text-cyan-300"
        >
            â† Back to Livestreams
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-cyan-400">
            Add Livestream
        </h1>

        <p class="mt-2 text-slate-400">
            {{ $tournament->title }}
        </p>
    </div>

    <div class="rounded-xl border border-slate-800 bg-slate-900 p-6">
        <form
            method="POST"
            action="{{ route('tournaments.livestreams.store', $tournament) }}"
        >
            @include('payments.livestreams._form')
        </form>
    </div>
</div>
@endsection