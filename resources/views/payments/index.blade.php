@extends('layouts.app')

@section('title', 'Payment Verification | ArenaSync')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
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
            Payment Verification
        </h1>

        <p class="mt-2 text-slate-400">
            Review submitted payments for
            <span class="font-semibold text-slate-200">
                {{ $tournament->title }}
            </span>
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-emerald-700 bg-emerald-950/50 p-4 text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
            <p class="font-semibold">
                Payment action could not be completed.
            </p>

            <ul class="mt-2 list-inside list-disc space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($payments->isEmpty())
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-10 text-center">
            <h2 class="text-xl font-semibold text-white">
                No Payments Submitted
            </h2>

            <p class="mt-2 text-slate-400">
                No approved participants have submitted payment yet.
            </p>
        </div>
    @else
        <div class="space-y-5">
            @foreach ($payments as $payment)
                <article class="rounded-xl border border-slate-800 bg-slate-900 p-6 shadow-xl">
                    <div class="flex flex-col justify-between gap-6 lg:flex-row">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="text-xl font-bold text-white">
                                    {{ $payment->registration->team->name }}
                                </h2>

                                @if ($payment->isPending())
                                    <span class="rounded-full bg-amber-950 px-3 py-1 text-xs font-semibold text-amber-300">
                                        PENDING
                                    </span>
                                @elseif ($payment->isVerified())
                                    <span class="rounded-full bg-emerald-950 px-3 py-1 text-xs font-semibold text-emerald-300">
                                        VERIFIED
                                    </span>
                                @else
                                    <span class="rounded-full bg-red-950 px-3 py-1 text-xs font-semibold text-red-300">
                                        REJECTED
                                    </span>
                                @endif
                            </div>

                            <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                <div class="rounded-lg bg-slate-950 p-4">
                                    <p class="text-xs uppercase tracking-wide text-slate-500">
                                        Amount
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-200">
                                        BDT {{ number_format((float) $payment->amount, 2) }}
                                    </p>
                                </div>

                                <div class="rounded-lg bg-slate-950 p-4">
                                    <p class="text-xs uppercase tracking-wide text-slate-500">
                                        Method
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-200">
                                        {{ $payment->method }}
                                    </p>
                                </div>

                                <div class="rounded-lg bg-slate-950 p-4">
                                    <p class="text-xs uppercase tracking-wide text-slate-500">
                                        Reference
                                    </p>

                                    <p class="mt-1 break-all font-semibold text-slate-200">
                                        {{ $payment->reference }}
                                    </p>
                                </div>

                                <div class="rounded-lg bg-slate-950 p-4">
                                    <p class="text-xs uppercase tracking-wide text-slate-500">
                                        Submitted
                                    </p>

                                    <p class="mt-1 font-semibold text-slate-200">
                                        {{ $payment->submitted_at?->format('M d, Y h:i A') ?? '—' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if ($payment->isPending())
                            <div class="flex shrink-0 flex-wrap items-start gap-3">
                                <form
                                    method="POST"
                                    action="{{ route(
                                        'tournaments.payments.verify',
                                        [
                                            $tournament,
                                            $payment,
                                        ]
                                    ) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="rounded-lg bg-emerald-600 px-5 py-2.5 font-semibold text-white transition hover:bg-emerald-500"
                                    >
                                        Verify
                                    </button>
                                </form>

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'tournaments.payments.reject',
                                        [
                                            $tournament,
                                            $payment,
                                        ]
                                    ) }}"
                                    onsubmit="return confirm('Reject this payment?');"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="rounded-lg bg-red-600 px-5 py-2.5 font-semibold text-white transition hover:bg-red-500"
                                    >
                                        Reject
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="text-sm text-slate-500">
                                Review completed
                            </div>
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