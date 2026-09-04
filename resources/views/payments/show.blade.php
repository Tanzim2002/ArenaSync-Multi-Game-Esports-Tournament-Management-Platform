@extends('layouts.app')

@section('title', 'Payment Status | ArenaSync')

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
            Payment Status
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
        @if (! $registration->tournament->requiresPayment())
            <div class="rounded-lg border border-cyan-800 bg-cyan-950/40 p-4 text-cyan-200">
                This tournament is free. No payment is required.
            </div>

        @elseif (! $registration->isApproved())
            <div class="rounded-lg border border-amber-700 bg-amber-950/40 p-4 text-amber-200">
                Your registration has not been approved yet.
            </div>

        @elseif ($registration->payment === null)
            <div>
                <p class="text-slate-300">
                    No payment has been submitted for this registration.
                </p>

                <a
                    href="{{ route('registrations.payment.create', $registration) }}"
                    class="mt-5 inline-flex rounded-lg bg-cyan-600 px-5 py-3 font-semibold text-white transition hover:bg-cyan-500"
                >
                    Submit Payment
                </a>
            </div>

        @else
            @php
                $payment = $registration->payment;
            @endphp

            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">
                        Amount
                    </p>

                    <p class="mt-1 text-2xl font-bold text-white">
                        BDT {{ number_format((float) $payment->amount, 2) }}
                    </p>
                </div>

                @if ($payment->isPending())
                    <span class="rounded-full bg-amber-950 px-4 py-2 text-sm font-semibold text-amber-300">
                        PENDING
                    </span>
                @elseif ($payment->isVerified())
                    <span class="rounded-full bg-emerald-950 px-4 py-2 text-sm font-semibold text-emerald-300">
                        VERIFIED
                    </span>
                @else
                    <span class="rounded-full bg-red-950 px-4 py-2 text-sm font-semibold text-red-300">
                        REJECTED
                    </span>
                @endif
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Payment Method
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $payment->method }}
                    </p>
                </div>

                <div class="rounded-lg bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Transaction Reference
                    </p>

                    <p class="mt-1 break-all font-semibold text-slate-200">
                        {{ $payment->reference }}
                    </p>
                </div>

                <div class="rounded-lg bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Submitted
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $payment->submitted_at?->format('M d, Y h:i A') ?? '—' }}
                    </p>
                </div>

                <div class="rounded-lg bg-slate-950 p-4">
                    <p class="text-sm text-slate-500">
                        Reviewed
                    </p>

                    <p class="mt-1 font-semibold text-slate-200">
                        {{ $payment->reviewed_at?->format('M d, Y h:i A') ?? 'Not reviewed yet' }}
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection