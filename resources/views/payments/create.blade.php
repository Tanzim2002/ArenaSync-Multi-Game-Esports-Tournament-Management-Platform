@extends('layouts.app')

@section('title', 'Submit Payment | ArenaSync')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a
            href="{{ route('tournaments.show', $registration->tournament) }}"
            class="text-sm font-medium text-cyan-400 transition hover:text-cyan-300"
        >
            ← Back to Tournament
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">
            Submit Payment
        </h1>

        <p class="mt-2 text-slate-400">
            {{ $registration->tournament->title }}
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-emerald-700 bg-emerald-950/50 p-4 text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('payment'))
        <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
            {{ $errors->first('payment') }}
        </div>
    @endif

    <div class="rounded-xl border border-slate-800 bg-slate-900 p-6 shadow-xl">
        <div class="mb-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-lg bg-slate-950 p-4">
                <p class="text-sm text-slate-500">
                    Tournament Type
                </p>

                <p class="mt-1 font-semibold text-white">
                    {{ $registration->tournament->is_paid ? 'Paid' : 'Free' }}
                </p>
            </div>

            <div class="rounded-lg bg-slate-950 p-4">
                <p class="text-sm text-slate-500">
                    Entry Fee
                </p>

                <p class="mt-1 font-semibold text-white">
                    BDT
                    {{ number_format(
                        (float) $registration->tournament->entry_fee,
                        2
                    ) }}
                </p>
            </div>
        </div>

        @if (! $registration->tournament->requiresPayment())
            <div class="rounded-lg border border-cyan-800 bg-cyan-950/40 p-4 text-cyan-200">
                This tournament is free. No payment is required.
            </div>

        @elseif (! $registration->isApproved())
            <div class="rounded-lg border border-amber-700 bg-amber-950/40 p-4 text-amber-200">
                Your registration must be approved before payment can be submitted.
            </div>

        @elseif ($registration->payment !== null)
            <div class="rounded-lg border border-slate-700 bg-slate-950 p-4">
                <p class="text-slate-300">
                    A payment has already been submitted for this registration.
                </p>

                <a
                    href="{{ route('registrations.payment.show', $registration) }}"
                    class="mt-4 inline-flex rounded-lg bg-cyan-600 px-5 py-2.5 font-semibold text-white transition hover:bg-cyan-500"
                >
                    View Payment Status
                </a>
            </div>

        @else
            <form
                method="POST"
                action="{{ route('registrations.payment.store', $registration) }}"
                class="space-y-6"
            >
                @csrf

                <div>
                    <label
                        for="amount"
                        class="mb-2 block text-sm font-medium text-slate-200"
                    >
                        Amount Paid
                    </label>

                    <input
                        id="amount"
                        name="amount"
                        type="number"
                        min="1"
                        step="0.01"
                        required
                        value="{{ old(
                            'amount',
                            $registration->tournament->entry_fee
                        ) }}"
                        class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none transition focus:border-cyan-500"
                    >

                    @error('amount')
                        <p class="mt-2 text-sm text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="method"
                        class="mb-2 block text-sm font-medium text-slate-200"
                    >
                        Payment Method
                    </label>

                    <select
                        id="method"
                        name="method"
                        required
                        class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none transition focus:border-cyan-500"
                    >
                        <option value="">
                            Select Payment Method
                        </option>

                        @foreach (
                            [
                                'bKash',
                                'Nagad',
                                'Rocket',
                                'Card',
                                'Bank Transfer',
                            ] as $method
                        )
                            <option
                                value="{{ $method }}"
                                @selected(old('method') === $method)
                            >
                                {{ $method }}
                            </option>
                        @endforeach
                    </select>

                    @error('method')
                        <p class="mt-2 text-sm text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="reference"
                        class="mb-2 block text-sm font-medium text-slate-200"
                    >
                        Transaction Reference
                    </label>

                    <input
                        id="reference"
                        name="reference"
                        type="text"
                        maxlength="150"
                        required
                        value="{{ old('reference') }}"
                        placeholder="Example: TXN-123456"
                        class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none transition focus:border-cyan-500"
                    >

                    @error('reference')
                        <p class="mt-2 text-sm text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="inline-flex rounded-lg bg-cyan-600 px-6 py-3 font-semibold text-white transition hover:bg-cyan-500"
                >
                    Submit Payment
                </button>
            </form>
        @endif
    </div>
</div>
@endsection