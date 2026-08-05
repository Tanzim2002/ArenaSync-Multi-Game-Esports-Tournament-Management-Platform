@extends('layouts.app')

@section('title', 'Games | ArenaSync')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-cyan-400">
                Competitive Games
            </h1>

            <p class="mt-2 text-slate-400">
                Browse games currently supported by ArenaSync.
            </p>
        </div>

        @auth
            @if (auth()->user()->hasRole(\App\Models\User::ROLE_ADMIN))
                <a
                    href="{{ route('games.create') }}"
                    class="rounded bg-cyan-600 px-5 py-3 font-semibold hover:bg-cyan-500"
                >
                    Add Game
                </a>
            @endif
        @endauth
    </div>

    @if ($games->isEmpty())
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-10 text-center">
            <p class="text-slate-400">
                No games have been added yet.
            </p>
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($games as $game)
                <article class="rounded-xl border border-slate-800 bg-slate-900 p-6">
                    <h2 class="text-2xl font-bold text-cyan-400">
                        {{ $game->name }}
                    </h2>

                    <div class="mt-4 space-y-2 text-slate-300">
                        <p>
                            <span class="font-semibold">Genre:</span>
                            {{ $game->genre }}
                        </p>

                        <p>
                            <span class="font-semibold">Platform:</span>
                            {{ $game->platform }}
                        </p>

                        <p>
                            <span class="font-semibold">Team size:</span>
                            {{ $game->team_size }}
                        </p>
                    </div>

                    <a
                        href="{{ route('games.show', $game) }}"
                        class="mt-6 inline-block text-cyan-400 hover:text-cyan-300"
                    >
                        View details →
                    </a>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $games->links() }}
        </div>
    @endif
@endsection