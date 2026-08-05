@extends('layouts.app')

@section('title', 'Edit Game | ArenaSync')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-cyan-400">
                Edit {{ $game->name }}
            </h1>

            <p class="mt-2 text-slate-400">
                Update the selected game's information and rules.
            </p>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-900 p-8">
            <form method="POST" action="{{ route('games.update', $game) }}">
                @csrf
                @method('PUT')

                @include('games._form', [
                    'buttonText' => 'Update Game',
                    'game' => $game,
                ])
            </form>
        </div>
    </div>
@endsection