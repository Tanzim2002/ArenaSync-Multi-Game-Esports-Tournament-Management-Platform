@extends('layouts.app')

@section('title', 'Submit Payment | ArenaSync')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <a href="{{ route('tournaments.show', $registration->tournament) }}" class="text-cyan-400 hover:text-cyan-300">
                ← Back to tournament
            </a>
        </div>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-cyan-400">Submit Payment</h1>
            <p class="mt-2 text-slate-400">
                {{ $registration->tournament->title }} — Entry fee: {{ number_format((float) $registration->tournament->entry_fee, 2) }}
            </p>
        </div>

        @if ($errors->has('payment'))
            <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
                {{ $errors->first('payment') }}
            </div>
        @endif

        <div class="rounded-xl border border-slate-800 bg-slate-900 p-6">
            <form method="POST" action="{{ route('registrations.payment.store', $registration) }}" class="space-y-6">
                @csrf

                <div>
                    <label for="amount" class="mb-2 block font-medium text-slate-200">Amount Paid</label>
                    <input
                        id="amount"
                        name="amount"
                        type="number"
                        step="0.01"
                        min="1"
                        value="{{ old('amount', $registration->tournament->entry_fee) }}"
                        required
                        class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
                    >
                    @error('amount')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="method" class="mb-2 block font-medium text-slate-200">Payment Method</label>
                    <select id="method" name="method" required class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white">
                        <option value="">Select a method</option>
                        @foreach (['bKash', 'Nagad', 'Rocket', 'Card', 'Bank Transfer'] as $option)
                            <option value="{{ $option }}" @selected(old('method') === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('method')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reference" class="mb-2 block font-medium text-slate-200">Transaction Reference</label>
                    <input
                        id="reference"
                        name="reference"
                        type="text"
                        value="{{ old('reference') }}"
                        required
                        maxlength="150"
                        placeholder="Example: TXN-48213"
                        class="w-full rounded border border-slate-700 bg-slate-950 px-4 py-3 text-white"
                    >
                    @error('reference')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="rounded bg-cyan-600 px-6 py-3 font-semibold text-white hover:bg-cyan-500">
                    Submit Payment
                </button>
            </form>
        </div>
    </div>
@endsection