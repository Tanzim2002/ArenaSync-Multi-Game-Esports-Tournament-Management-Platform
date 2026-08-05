@extends('layouts.app')

@section('title', 'Add Game | ArenaSync')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-cyan-400">
                Add New Game
            </h1>

            <p class="mt-2 text-slate-400">
                Add a competitive game to the ArenaSync platform.
            </p>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-900 p-8">
            <form method="POST" action="{{ route('games.store') }}">
                @csrf

                @include('games._form', [
                    'buttonText' => 'Create Game',
                ])
            </form>
        </div>
    </div>
@endsection