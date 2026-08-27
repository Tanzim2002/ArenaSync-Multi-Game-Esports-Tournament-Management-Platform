@extends('layouts.app')

@section('title', 'Payment Verification | ArenaSync')

@section('content')
    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <a href="{{ route('tournaments.show', $tournament) }}" class="text-cyan-400 hover:text-cyan-300">
                ← Back to tournament
            </a>
        </div>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-cyan-400">Payment Verification</h1>
            <p class="mt-2 text-slate-400">
                Review payments submitted for
                <span class="font-semibold text-slate-200">{{ $tournament->title }}</span>.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
                <p class="font-semibold">Payment action could not be completed.</p>
                <ul class="mt-2 list-inside list-disc space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($payments->isEmpty())
            <div class="rounded-xl border border-slate-800 bg-slate-900 p-10 text-center">
                <h2 class="text-xl font-semibold text-white">No payments submitted</h2>
                <p class="mt-2 text-slate-400">No teams have submitted payment for this tournament yet.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($payments as $payment)
                    <article class="rounded-xl border border-slate-800 bg-slate-900 p-6">
                        <div class="flex flex-wrap items-start justify-between gap-6">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-xl font-bold text-white">
                                        {{ $payment->registration->team->name }}
                                    </h2>

                                    @if ($payment->status === \App\Models\Payment::STATUS_PENDING)
                                        <span class="rounded-full bg-amber-950 px-3 py-1 text-xs font-semibold text-amber-300">PENDING</span>
                                    @elseif ($payment->status === \App\Models\Payment::STATUS_VERIFIED)
                                        <span class="rounded-full bg-emerald-950 px-3 py-1 text-xs font-semibold text-emerald-300">VERIFIED</span>
                                    @else
                                        <span class="rounded-full bg-red-950 px-3 py-1 text-xs font-semibold text-red-300">REJECTED</span>
                                    @endif
                                </div>

                                <div class="mt-4 grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                                    <div>
                                        <p class="text-slate-500">Amount</p>
                                        <p class="mt-1 font-semibold text-slate-200">{{ number_format((float) $payment->amount, 2) }}</p>
                                    </div>
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
                            </div>

                            @if ($payment->isPending())
                                <div class="flex flex-wrap gap-3">
                                    <form method="POST" action="{{ route('tournaments.payments.verify', [$tournament, $payment]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-500">
                                            Verify
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('tournaments.payments.reject', [$tournament, $payment]) }}" onsubmit="return confirm('Reject this payment?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-500">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="text-sm text-slate-500">Decision completed</div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
@endsection