@extends('layouts.app')

@section('title', 'Tournament Pricing | ArenaSync')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a
            href="{{ route('tournaments.show', $tournament) }}"
            class="text-sm font-medium text-cyan-400 transition hover:text-cyan-300"
        >
            ← Back to Tournament
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">
            Tournament Pricing
        </h1>

        <p class="mt-2 text-slate-400">
            {{ $tournament->title }}
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-emerald-700 bg-emerald-950/50 p-4 text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl border border-slate-800 bg-slate-900 p-6 shadow-xl">
        <form
            method="POST"
            action="{{ route('tournaments.pricing.update', $tournament) }}"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            <div>
                <p class="mb-3 text-sm font-medium text-slate-200">
                    Tournament Type
                </p>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="cursor-pointer rounded-lg border border-slate-700 bg-slate-950 p-4">
                        <div class="flex items-center gap-3">
                            <input
                                type="radio"
                                name="is_paid"
                                value="0"
                                @checked(
                                    old(
                                        'is_paid',
                                        $tournament->is_paid ? '1' : '0'
                                    ) === '0'
                                )
                                onchange="toggleEntryFee(false)"
                                class="h-4 w-4"
                            >

                            <div>
                                <p class="font-semibold text-white">
                                    Free Tournament
                                </p>

                                <p class="text-sm text-slate-400">
                                    Participants do not need to submit payment.
                                </p>
                            </div>
                        </div>
                    </label>

                    <label class="cursor-pointer rounded-lg border border-slate-700 bg-slate-950 p-4">
                        <div class="flex items-center gap-3">
                            <input
                                type="radio"
                                name="is_paid"
                                value="1"
                                @checked(
                                    old(
                                        'is_paid',
                                        $tournament->is_paid ? '1' : '0'
                                    ) === '1'
                                )
                                onchange="toggleEntryFee(true)"
                                class="h-4 w-4"
                            >

                            <div>
                                <p class="font-semibold text-white">
                                    Paid Tournament
                                </p>

                                <p class="text-sm text-slate-400">
                                    Verified payment is required for eligibility.
                                </p>
                            </div>
                        </div>
                    </label>
                </div>

                @error('is_paid')
                    <p class="mt-2 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div
                id="entry-fee-wrapper"
                class="{{ old(
                    'is_paid',
                    $tournament->is_paid ? '1' : '0'
                ) === '1' ? '' : 'hidden' }}"
            >
                <label
                    for="entry_fee"
                    class="mb-2 block text-sm font-medium text-slate-200"
                >
                    Entry Fee (BDT)
                </label>

                <input
                    id="entry_fee"
                    name="entry_fee"
                    type="number"
                    min="1"
                    max="100000"
                    step="0.01"
                    value="{{ old('entry_fee', $tournament->entry_fee) }}"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none transition focus:border-cyan-500"
                >

                @error('entry_fee')
                    <p class="mt-2 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button
                type="submit"
                class="rounded-lg bg-cyan-600 px-6 py-3 font-semibold text-white transition hover:bg-cyan-500"
            >
                Save Pricing
            </button>
        </form>
    </div>
</div>

<script>
    function toggleEntryFee(isPaid) {
        const wrapper =
            document.getElementById('entry-fee-wrapper');

        const input =
            document.getElementById('entry_fee');

        if (isPaid) {
            wrapper.classList.remove('hidden');
            return;
        }

        wrapper.classList.add('hidden');
        input.value = '';
    }
</script>
@endsection