<div class="space-y-5">
    <div>
        <label for="name" class="mb-2 block font-medium">
            Game Name
        </label>

        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $game->name ?? '') }}"
            required
            maxlength="150"
            class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3"
        >

        @error('name')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="genre" class="mb-2 block font-medium">
            Genre
        </label>

        <input
            id="genre"
            name="genre"
            type="text"
            value="{{ old('genre', $game->genre ?? '') }}"
            required
            maxlength="100"
            class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3"
        >

        @error('genre')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="platform" class="mb-2 block font-medium">
            Platform
        </label>

        <input
            id="platform"
            name="platform"
            type="text"
            value="{{ old('platform', $game->platform ?? '') }}"
            required
            maxlength="100"
            class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3"
        >

        @error('platform')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="team_size" class="mb-2 block font-medium">
            Team Size
        </label>

        <input
            id="team_size"
            name="team_size"
            type="number"
            value="{{ old('team_size', $game->team_size ?? 1) }}"
            required
            min="1"
            max="100"
            class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3"
        >

        @error('team_size')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="rules" class="mb-2 block font-medium">
            Competitive Rules
        </label>

        <textarea
            id="rules"
            name="rules"
            rows="8"
            required
            maxlength="5000"
            class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3"
        >{{ old('rules', $game->rules ?? '') }}</textarea>

        @error('rules')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex gap-4">
        <button
            type="submit"
            class="rounded bg-cyan-600 px-6 py-3 font-semibold hover:bg-cyan-500"
        >
            {{ $buttonText }}
        </button>

        <a
            href="{{ route('games.index') }}"
            class="rounded border border-slate-600 px-6 py-3 hover:bg-slate-800"
        >
            Cancel
        </a>
    </div>
</div>