@csrf

<div class="space-y-6">
    <div>
        <label
            for="platform"
            class="mb-2 block text-sm font-medium text-slate-200"
        >
            Platform
        </label>

        <select
            id="platform"
            name="platform"
            required
            class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none transition focus:border-cyan-500"
        >
            <option value="">Select a platform</option>

            @foreach ([
                'youtube' => 'YouTube',
                'twitch' => 'Twitch',
                'facebook' => 'Facebook',
            ] as $value => $label)
                <option
                    value="{{ $value }}"
                    @selected(
                        old(
                            'platform',
                            $livestream->platform ?? ''
                        ) === $value
                    )
                >
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('platform')
            <p class="mt-2 text-sm text-red-400">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label
            for="url"
            class="mb-2 block text-sm font-medium text-slate-200"
        >
            Secure Stream URL
        </label>

        <input
            id="url"
            name="url"
            type="url"
            required
            value="{{ old('url', $livestream->url ?? '') }}"
            placeholder="https://..."
            class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none transition focus:border-cyan-500"
        >

        <p class="mt-2 text-xs text-slate-500">
            Use an HTTPS YouTube, Twitch, or Facebook stream link.
        </p>

        @error('url')
            <p class="mt-2 text-sm text-red-400">
                {{ $message }}
            </p>
        @enderror
    </div>

    <div>
        <label
            for="label"
            class="mb-2 block text-sm font-medium text-slate-200"
        >
            Label
        </label>

        <input
            id="label"
            name="label"
            type="text"
            maxlength="255"
            value="{{ old('label', $livestream->label ?? '') }}"
            placeholder="Example: Main Stream"
            class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none transition focus:border-cyan-500"
        >

        @error('label')
            <p class="mt-2 text-sm text-red-400">
                {{ $message }}
            </p>
        @enderror
    </div>

    @isset($livestream)
        <div class="rounded-lg border border-slate-800 bg-slate-950 p-4">
            <input
                type="hidden"
                name="is_active"
                value="0"
            >

            <label class="flex cursor-pointer items-center gap-3">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    @checked(
                        old(
                            'is_active',
                            $livestream->is_active
                        )
                    )
                    class="h-4 w-4"
                >

                <span>
                    <span class="block font-medium text-white">
                        Active stream
                    </span>

                    <span class="block text-sm text-slate-400">
                        Inactive streams stay hidden from public viewers.
                    </span>
                </span>
            </label>
        </div>
    @endisset

    <div class="flex flex-wrap gap-3">
        <button
            type="submit"
            class="rounded-lg bg-cyan-600 px-6 py-3 font-semibold text-white transition hover:bg-cyan-500"
        >
            Save Livestream
        </button>

        <a
            href="{{ route('tournaments.livestreams.index', $tournament) }}"
            class="rounded-lg bg-slate-700 px-6 py-3 font-semibold text-white transition hover:bg-slate-600"
        >
            Cancel
        </a>
    </div>
</div>