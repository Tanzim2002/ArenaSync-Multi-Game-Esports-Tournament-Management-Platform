<div class="space-y-6">
    <div>
        <label
            for="game_id"
            class="mb-2 block font-medium text-slate-200"
        >
            Game
        </label>

        <select
            id="game_id"
            name="game_id"
            required
            class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
        >
            <option value="">
                Select a game
            </option>

            @foreach ($games as $game)
                <option
                    value="{{ $game->id }}"
                    @selected(
                        old(
                            'game_id',
                            $tournament->game_id ?? ''
                        ) == $game->id
                    )
                >
                    {{ $game->name }} — {{ $game->platform }}
                </option>
            @endforeach
        </select>

        @error('game_id')
            <p class="mt-1 text-sm text-red-400">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <div>
            <label
                for="category"
                class="mb-2 block font-medium text-slate-200"
            >
                Category
            </label>

            <input
                id="category"
                name="category"
                type="text"
                value="{{ old('category', $tournament->category ?? '') }}"
                required
                maxlength="100"
                placeholder="Example: Competitive"
                class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
            >

            @error('category')
                <p class="mt-1 text-sm text-red-400">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="region"
                class="mb-2 block font-medium text-slate-200"
            >
                Region
            </label>

            <input
                id="region"
                name="region"
                type="text"
                value="{{ old('region', $tournament->region ?? '') }}"
                required
                maxlength="100"
                placeholder="Example: South Asia"
                class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
            >

            @error('region')
                <p class="mt-1 text-sm text-red-400">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="prize_type"
                class="mb-2 block font-medium text-slate-200"
            >
                Prize Type
            </label>

            <input
                id="prize_type"
                name="prize_type"
                type="text"
                value="{{ old('prize_type', $tournament->prize_type ?? '') }}"
                required
                maxlength="50"
                placeholder="Example: Cash"
                class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
            >

            @error('prize_type')
                <p class="mt-1 text-sm text-red-400">
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    <div>
        <label
            for="title"
            class="mb-2 block font-medium text-slate-200"
        >
            Tournament Title
        </label>

        <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $tournament->title ?? '') }}"
            required
            maxlength="180"
            class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
        >

        @error('title')
            <p class="mt-1 text-sm text-red-400">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label
            for="description"
            class="mb-2 block font-medium text-slate-200"
        >
            Description
        </label>

        <textarea
            id="description"
            name="description"
            rows="5"
            required
            maxlength="10000"
            class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
        >{{ old('description', $tournament->description ?? '') }}</textarea>

        @error('description')
            <p class="mt-1 text-sm text-red-400">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <div>
            <label
                for="registration_deadline"
                class="mb-2 block font-medium text-slate-200"
            >
                Registration Deadline
            </label>

            <input
                id="registration_deadline"
                name="registration_deadline"
                type="datetime-local"
                value="{{ old(
                    'registration_deadline',
                    isset($tournament)
                        ? $tournament->registration_deadline?->format('Y-m-d\TH:i')
                        : ''
                ) }}"
                required
                class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
            >

            @error('registration_deadline')
                <p class="mt-1 text-sm text-red-400">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="start_at"
                class="mb-2 block font-medium text-slate-200"
            >
                Start Date & Time
            </label>

            <input
                id="start_at"
                name="start_at"
                type="datetime-local"
                value="{{ old(
                    'start_at',
                    isset($tournament)
                        ? $tournament->start_at?->format('Y-m-d\TH:i')
                        : ''
                ) }}"
                required
                class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
            >

            @error('start_at')
                <p class="mt-1 text-sm text-red-400">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="end_at"
                class="mb-2 block font-medium text-slate-200"
            >
                End Date & Time
            </label>

            <input
                id="end_at"
                name="end_at"
                type="datetime-local"
                value="{{ old(
                    'end_at',
                    isset($tournament)
                        ? $tournament->end_at?->format('Y-m-d\TH:i')
                        : ''
                ) }}"
                required
                class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
            >

            @error('end_at')
                <p class="mt-1 text-sm text-red-400">
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <div>
            <label
                for="prize_pool"
                class="mb-2 block font-medium text-slate-200"
            >
                Prize Pool
            </label>

            <input
                id="prize_pool"
                name="prize_pool"
                type="number"
                step="0.01"
                min="0"
                value="{{ old('prize_pool', $tournament->prize_pool ?? 0) }}"
                required
                class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
            >

            @error('prize_pool')
                <p class="mt-1 text-sm text-red-400">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="team_limit"
                class="mb-2 block font-medium text-slate-200"
            >
                Team Limit
            </label>

            <input
                id="team_limit"
                name="team_limit"
                type="number"
                min="2"
                max="1000"
                value="{{ old('team_limit', $tournament->team_limit ?? 2) }}"
                required
                class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
            >

            @error('team_limit')
                <p class="mt-1 text-sm text-red-400">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="match_format"
                class="mb-2 block font-medium text-slate-200"
            >
                Match Format
            </label>

            <input
                id="match_format"
                name="match_format"
                type="text"
                value="{{ old('match_format', $tournament->match_format ?? '') }}"
                required
                maxlength="100"
                placeholder="Example: Single Elimination"
                class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
            >

            @error('match_format')
                <p class="mt-1 text-sm text-red-400">
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    <div>
        <label
            for="rules"
            class="mb-2 block font-medium text-slate-200"
        >
            Tournament Rules
        </label>

        <textarea
            id="rules"
            name="rules"
            rows="8"
            required
            maxlength="10000"
            class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
        >{{ old('rules', $tournament->rules ?? '') }}</textarea>

        @error('rules')
            <p class="mt-1 text-sm text-red-400">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="flex flex-wrap gap-4">
        <button
            type="submit"
            class="rounded bg-cyan-600 px-6 py-3 font-semibold text-white hover:bg-cyan-500"
        >
            {{ $buttonText }}
        </button>

        <a
            href="{{ $cancelUrl }}"
            class="rounded border border-slate-600 px-6 py-3 text-slate-200 hover:bg-slate-800"
        >
            Cancel
        </a>
    </div>
</div>