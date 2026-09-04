<div class="space-y-6">
    <div>
        <label
            for="tournament_id"
            class="mb-2 block text-sm font-medium text-slate-200"
        >
            Tournament
        </label>

        <select
            id="tournament_id"
            name="tournament_id"
            required
            class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-500"
        >
            <option value="">Select a tournament</option>

            @foreach ($tournaments as $tournament)
                <option
                    value="{{ $tournament->id }}"
                    @selected(
                        (string) old(
                            'tournament_id',
                            $sponsor->tournament_id ?? ''
                        )
                        ===
                        (string) $tournament->id
                    )
                >
                    {{ $tournament->title }}
                </option>
            @endforeach
        </select>

        @error('tournament_id')
            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="name" class="mb-2 block text-sm font-medium text-slate-200">
                Sponsor Name
            </label>

            <input
                id="name"
                name="name"
                type="text"
                required
                value="{{ old('name', $sponsor->name ?? '') }}"
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-500"
            >

            @error('name')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="sponsorship_type" class="mb-2 block text-sm font-medium text-slate-200">
                Sponsorship Type
            </label>

            <input
                id="sponsorship_type"
                name="sponsorship_type"
                type="text"
                value="{{ old('sponsorship_type', $sponsor->sponsorship_type ?? '') }}"
                placeholder="Example: Title Sponsor"
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-500"
            >
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="logo" class="mb-2 block text-sm font-medium text-slate-200">
                Logo URL
            </label>

            <input
                id="logo"
                name="logo"
                type="url"
                value="{{ old('logo', $sponsor->logo ?? '') }}"
                placeholder="https://..."
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-500"
            >
        </div>

        <div>
            <label for="website" class="mb-2 block text-sm font-medium text-slate-200">
                Website
            </label>

            <input
                id="website"
                name="website"
                type="url"
                value="{{ old('website', $sponsor->website ?? '') }}"
                placeholder="https://..."
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-500"
            >
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <div>
            <label for="contact_person" class="mb-2 block text-sm font-medium text-slate-200">
                Contact Person
            </label>

            <input
                id="contact_person"
                name="contact_person"
                type="text"
                value="{{ old('contact_person', $sponsor->contact_person ?? '') }}"
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-500"
            >
        </div>

        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-slate-200">
                Email
            </label>

            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $sponsor->email ?? '') }}"
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-500"
            >
        </div>

        <div>
            <label for="phone" class="mb-2 block text-sm font-medium text-slate-200">
                Phone
            </label>

            <input
                id="phone"
                name="phone"
                type="text"
                value="{{ old('phone', $sponsor->phone ?? '') }}"
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-500"
            >
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="amount" class="mb-2 block text-sm font-medium text-slate-200">
                Amount
            </label>

            <input
                id="amount"
                name="amount"
                type="number"
                min="0"
                step="0.01"
                value="{{ old('amount', $sponsor->amount ?? '') }}"
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-500"
            >
        </div>

        <div>
            <label for="status" class="mb-2 block text-sm font-medium text-slate-200">
                Status
            </label>

            <select
                id="status"
                name="status"
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-500"
            >
                <option
                    value="1"
                    @selected(
                        (string) old(
                            'status',
                            isset($sponsor) ? (int) $sponsor->status : 1
                        ) === '1'
                    )
                >
                    Active
                </option>

                <option
                    value="0"
                    @selected(
                        (string) old(
                            'status',
                            isset($sponsor) ? (int) $sponsor->status : 1
                        ) === '0'
                    )
                >
                    Inactive
                </option>
            </select>
        </div>
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-medium text-slate-200">
            Description
        </label>

        <textarea
            id="description"
            name="description"
            rows="5"
            class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none focus:border-cyan-500"
        >{{ old('description', $sponsor->description ?? '') }}</textarea>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
            <ul class="list-inside list-disc space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>