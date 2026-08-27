@extends('layouts.app')

@section('title', $match->teamOne->name . ' vs ' . ($match->teamTwo->name ?? 'Bye') . ' | ArenaSync')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <a href="{{ route('tournaments.matches.index', $match->tournament) }}" class="text-cyan-400 hover:text-cyan-300">
                ← Back to match timeline
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded border border-green-500 bg-green-950 p-4 text-green-200">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <article class="rounded-xl border border-slate-800 bg-slate-900 p-8">
            <p class="text-sm text-slate-500">{{ $match->tournament->title }} — {{ $match->round }}</p>
            <h1 class="mt-1 text-3xl font-bold text-white">
                {{ $match->teamOne->name }} vs {{ $match->teamTwo->name ?? 'Bye' }}
            </h1>
            <p class="mt-3 text-slate-400">{{ $match->scheduled_at->format('M d, Y h:i A') }}</p>

            <span class="mt-4 inline-block rounded-full bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-300">
                {{ $match->status }}
            </span>

            @auth
                @if (auth()->user()->hasRole(\App\Models\User::ROLE_ORGANIZER) && auth()->id() === $match->tournament->organizer_id)
                    <div class="mt-8 flex flex-wrap gap-3">
                        @foreach ($match->allowedStatusTransitions() as $nextStatus)
                            <form method="POST" action="{{ route('matches.status.update', $match) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $nextStatus }}">
                                <button type="submit" class="rounded bg-cyan-600 px-4 py-2 font-semibold text-white hover:bg-cyan-500">
                                    Move to {{ $nextStatus }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                @endif
            @endauth
        </article>
    </div>
@endsection