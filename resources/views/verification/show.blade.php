@extends('layouts.app')

@section('title', 'Organizer Verification | ArenaSync')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-8">
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-3xl font-bold text-cyan-400">
                    {{ auth()->user()->name }}
                </h1>

                @if ($verification?->isVerified())
                    <span class="rounded-full border border-emerald-500 bg-emerald-950 px-3 py-1 text-sm font-semibold text-emerald-300">
                        ✓ Verified Organizer
                    </span>
                @endif
            </div>

            <p class="mt-3 text-slate-400">
                Organizer Verification
            </p>

            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
                    <ul class="list-inside list-disc space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($verification === null)
                <div class="mt-8 rounded-lg border border-slate-700 bg-slate-950 p-6">
                    <h2 class="text-xl font-semibold text-white">
                        Not Yet Requested
                    </h2>

                    <p class="mt-2 text-slate-400">
                        Submit a verification request for administrator review.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('organizer-verification.store') }}"
                        class="mt-5"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="rounded bg-cyan-600 px-5 py-2 font-semibold text-white hover:bg-cyan-500"
                        >
                            Request Verification
                        </button>
                    </form>
                </div>
            @else
                <div class="mt-8 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg border border-slate-800 bg-slate-950 p-5">
                        <p class="text-sm text-slate-500">
                            Current Status
                        </p>

                        <p class="mt-2 text-lg font-semibold
                            @if ($verification->isVerified())
                                text-emerald-300
                            @elseif ($verification->isRejected())
                                text-red-300
                            @else
                                text-amber-300
                            @endif
                        ">
                            {{ $verification->status }}
                        </p>
                    </div>

                    <div class="rounded-lg border border-slate-800 bg-slate-950 p-5">
                        <p class="text-sm text-slate-500">
                            Requested At
                        </p>

                        <p class="mt-2 font-semibold text-slate-200">
                            {{ $verification->requested_at->format('M d, Y h:i A') }}
                        </p>
                    </div>

                    @if ($verification->reviewed_at)
                        <div class="rounded-lg border border-slate-800 bg-slate-950 p-5">
                            <p class="text-sm text-slate-500">
                                Reviewed At
                            </p>

                            <p class="mt-2 font-semibold text-slate-200">
                                {{ $verification->reviewed_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    @endif

                    @if ($verification->reviewer)
                        <div class="rounded-lg border border-slate-800 bg-slate-950 p-5">
                            <p class="text-sm text-slate-500">
                                Reviewed By
                            </p>

                            <p class="mt-2 font-semibold text-slate-200">
                                {{ $verification->reviewer->name }}
                            </p>
                        </div>
                    @endif
                </div>

                @if ($verification->isPending())
                    <div class="mt-6 rounded-lg border border-amber-700 bg-amber-950/30 p-5 text-amber-200">
                        Your verification request is waiting for administrator review.
                    </div>
                @elseif ($verification->isVerified())
                    <div class="mt-6 rounded-lg border border-emerald-700 bg-emerald-950/30 p-5 text-emerald-200">
                        Your organizer account has been verified.
                    </div>
                @elseif ($verification->isRejected())
                    <div class="mt-6 rounded-lg border border-red-700 bg-red-950/30 p-5">
                        <p class="text-red-200">
                            Your previous verification request was rejected.
                            You may submit a new request.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('organizer-verification.store') }}"
                            class="mt-4"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="rounded bg-cyan-600 px-5 py-2 font-semibold text-white hover:bg-cyan-500"
                            >
                                Resubmit Verification
                            </button>
                        </form>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
