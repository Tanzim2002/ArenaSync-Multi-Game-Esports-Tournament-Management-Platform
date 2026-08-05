@extends('layouts.app')

@section('title', 'Register | ArenaSync')

@section('content')
    <div class="mx-auto max-w-lg rounded-xl border border-slate-800 bg-slate-900 p-8">
        <h1 class="mb-6 text-3xl font-bold">Create Account</h1>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="mb-2 block">Name</label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3"
                >

                @error('name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-2 block">Email</label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3"
                >

                @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="mb-2 block">Account Type</label>

                <select
                    id="role"
                    name="role"
                    required
                    class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3"
                >
                    <option value="PLAYER" @selected(old('role', 'PLAYER') === 'PLAYER')>
                        Player
                    </option>

                    <option value="ORGANIZER" @selected(old('role') === 'ORGANIZER')>
                        Organizer
                    </option>
                </select>

                @error('role')
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

            <div>
                <label for="password_confirmation" class="mb-2 block">
                    Confirm Password
                </label>

                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3"
                >
            </div>

            <button
                type="submit"
                class="w-full rounded bg-cyan-600 px-4 py-3 font-semibold hover:bg-cyan-500"
            >
                Register
            </button>
        </form>
    </div>
@endsection