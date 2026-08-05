@extends('layouts.app')

@section('title', 'Login | ArenaSync')

@section('content')
    <div class="mx-auto max-w-lg rounded-xl border border-slate-800 bg-slate-900 p-8">
        <h1 class="mb-6 text-3xl font-bold">Login</h1>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="mb-2 block">Email</label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3"
                >

                @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-2 block">Password</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3"
                >

                @error('password')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" value="1">
                <span>Remember me</span>
            </label>

            <button
                type="submit"
                class="w-full rounded bg-cyan-600 px-4 py-3 font-semibold hover:bg-cyan-500"
            >
                Login
            </button>
        </form>
    </div>
@endsection