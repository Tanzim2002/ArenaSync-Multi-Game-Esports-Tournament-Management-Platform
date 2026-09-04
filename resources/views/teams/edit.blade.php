@extends('layouts.app')

@section('title', 'Edit ' . $team->name . ' | ArenaSync')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a
            href="{{ route('teams.show', $team) }}"
            class="text-sm font-medium text-cyan-400 transition hover:text-cyan-300"
        >
            ← Back to Team Profile
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-cyan-400">
            Edit Team
        </h1>

        <p class="mt-2 text-slate-400">
            Update {{ $team->name }}.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
            <p class="font-semibold">
                Please fix the following problems:
            </p>

            <ul class="mt-2 list-inside list-disc space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl border border-slate-800 bg-slate-900 p-6 shadow-xl">
        <form
            action="{{ route('teams.update', $team) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            <div>
                <label
                    for="name"
                    class="mb-2 block text-sm font-medium text-slate-200"
                >
                    Team Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $team->name) }}"
                    required
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none transition focus:border-cyan-500"
                >

                @error('name')
                    <p class="mt-2 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label
                    for="preferred_game_id"
                    class="mb-2 block text-sm font-medium text-slate-200"
                >
                    Preferred Game
                </label>

                <select
                    id="preferred_game_id"
                    name="preferred_game_id"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none transition focus:border-cyan-500"
                >
                    <option value="">
                        Select a game
                    </option>

                    @foreach ($games as $game)
                        <option
                            value="{{ $game->id }}"
                            @selected(
                                (string) old(
                                    'preferred_game_id',
                                    $team->preferred_game_id
                                )
                                ===
                                (string) $game->id
                            )
                        >
                            {{ $game->name }}
                            @if ($game->platform)
                                — {{ $game->platform }}
                            @endif
                        </option>
                    @endforeach
                </select>

                @error('preferred_game_id')
                    <p class="mt-2 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            @if ($team->logo)
                <div>
                    <p class="mb-2 text-sm font-medium text-slate-200">
                        Current Logo
                    </p>

                    <img
                        src="{{ asset('storage/' . $team->logo) }}"
                        alt="{{ $team->name }} logo"
                        class="h-28 w-28 rounded-xl border border-slate-700 object-cover"
                    >
                </div>
            @endif

            <div>
                <label
                    for="logo"
                    class="mb-2 block text-sm font-medium text-slate-200"
                >
                    {{ $team->logo ? 'Replace Logo' : 'Team Logo' }}
                </label>

                <input
                    type="file"
                    id="logo"
                    name="logo"
                    accept=".jpg,.jpeg,.png,.webp"
                    class="block w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-300 file:mr-4 file:rounded file:border-0 file:bg-cyan-600 file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-cyan-500"
                >

                @error('logo')
                    <p class="mt-2 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label
                    for="description"
                    class="mb-2 block text-sm font-medium text-slate-200"
                >
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none transition focus:border-cyan-500"
                >{{ old('description', $team->description) }}</textarea>

                @error('description')
                    <p class="mt-2 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex flex-wrap gap-3">
                <button
                    type="submit"
                    class="rounded-lg bg-cyan-600 px-6 py-3 font-semibold text-white transition hover:bg-cyan-500"
                >
                    Update Team
                </button>

                <a
                    href="{{ route('teams.show', $team) }}"
                    class="rounded-lg bg-slate-700 px-6 py-3 font-semibold text-white transition hover:bg-slate-600"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection