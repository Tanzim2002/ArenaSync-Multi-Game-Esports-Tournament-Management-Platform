@extends('layouts.app')

@section('title', $game->name . ' | ArenaSync')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <a
                href="{{ route('games.index') }}"
                class="text-cyan-400 hover:text-cyan-300"
            >
                ← Back to games
            </a>
        </div>

        <article class="rounded-xl border border-slate-800 bg-slate-900 p-8">
            <div class="flex flex-wrap items-start justify-between gap-6">
                <div>
                    <h1 class="text-4xl font-bold text-cyan-400">
                        {{ $game->name }}
                    </h1>

                    <div class="mt-4 flex flex-wrap gap-3">
                        <span class="rounded bg-slate-800 px-3 py-1">
                            {{ $game->genre }}
                        </span>

                        <span class="rounded bg-slate-800 px-3 py-1">
                            {{ $game->platform }}
                        </span>

                        <span class="rounded bg-slate-800 px-3 py-1">
                            Team size: {{ $game->team_size }}
                        </span>
                    </div>
                </div>

                @auth
                    @if (auth()->user()->hasRole(\App\Models\User::ROLE_ADMIN))
                        <div class="flex gap-3">
                            <a
                                href="{{ route('games.edit', $game) }}"
                                class="rounded bg-amber-600 px-4 py-2 hover:bg-amber-500"
                            >
                                Edit
                            </a>

                            <form
                                method="POST"
                                action="{{ route('games.destroy', $game) }}"
                                onsubmit="return confirm('Are you sure you want to delete this game?');"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="rounded bg-red-600 px-4 py-2 hover:bg-red-500"
                                >
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>

            <div class="mt-10">
                <h2 class="text-2xl font-semibold">
                    Competitive Rules
                </h2>

                <div class="mt-4 whitespace-pre-line leading-7 text-slate-300">{{ $game->rules }}</div>
            </div>
        </article>
    </div>
@endsection