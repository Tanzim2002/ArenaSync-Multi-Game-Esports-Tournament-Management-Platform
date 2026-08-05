@extends('layouts.app')

@section('title', 'ArenaSync')

@section('content')
    <section class="py-20 text-center">
        <h1 class="text-5xl font-bold text-cyan-400">
            ArenaSync
        </h1>

        <p class="mx-auto mt-5 max-w-2xl text-lg text-slate-300">
            Multi-Game Esports Tournament Management Platform
        </p>

        @auth
            <p class="mt-8 text-green-300">
                Welcome, {{ auth()->user()->name }}.
                You are logged in as {{ auth()->user()->role }}.
            </p>
        @else
            <div class="mt-8 flex justify-center gap-4">
                <a
                    href="{{ route('login') }}"
                    class="rounded border border-cyan-500 px-6 py-3"
                >
                    Login
                </a>

                <a
                    href="{{ route('register') }}"
                    class="rounded bg-cyan-600 px-6 py-3"
                >
                    Create Account
                </a>
            </div>
        @endauth
    </section>
@endsection