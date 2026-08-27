@extends('layouts.app')

@section('title', 'Payment Status | ArenaSync')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <a href="{{ route('tournaments.show', $registration->tournament) }}" class="text-cyan-400 hover:text-cyan-300">
                ← Back to tournament
            </a>
        </div>

        <h1 class="text-3xl font-bold text-cyan-400">Payment Status</h1>
        <p class="mt-2 text-slate-400">{{ $registration->tournament->title }}</p>

        @if ($errors->has('payment'))
            <div class="mt-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
                {{ $errors->first('payment') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mt-6 rounded-lg border border-green-500 bg-green-950 p-4 text-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-6 rounded-xl border border-slate-800 bg-slate-900 p-6">
            @if (! $registration->tournament->is_paid)
                <p class="text-slate-300">This tournament is free. No payment is required.</p>
            @elseif (! $registration->isApproved())
                <p class="text-amber-300">Your registration is not yet approved. Payment can be submitted once it's approved.</p>
            @elseif ($registration->payment === null)
                <p class="mb-5 text-slate-300">No payment has been submitted yet.</p>
                
                    href="{{ route('registrations.payment.create', $registration) }}"
                    class="inline-block rounded bg-cyan-600 px-5 py-3 font-semibold text-white hover:bg-cyan-500"
                >
                    Submit Payment
                </a>
            @else
                @php $payment = $registration->payment; @endphp

                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-xl font-bold text-white">{{ number_format((float) $payment->amount, 2) }}</h2>

                    @if ($payment->status === \App\Models\Payment::STATUS_PENDING)
                        <span class="rounded-full bg-amber-950 px-3 py-1 text-xs font-semibold text-amber-300">PENDING</span>
                    @elseif ($payment->status === \App\Models\Payment::STATUS_VERIFIED)
                        <span class="rounded-full bg-emerald-950 px-3 py-1 text-xs font-semibold text-emerald-300">VERIFIED</span>
                    @else
                        <span class="rounded-full bg-red-950 px-3 py-1 text-xs font-semibold text-red-300">REJECTED</span>
                    @endif
                </div>

                <div class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <p class="text-slate-500">Method</p>
                        <p class="mt-1 font-semibold text-slate-200">{{ $payment->method }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Reference</p>
                        <p class="mt-1 font-semibold text-slate-200">{{ $payment->reference }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Submitted</p>
                        <p class="mt-1 font-semibold text-slate-200">{{ $payment->submitted_at?->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection