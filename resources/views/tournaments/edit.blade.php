@extends('layouts.app')

@section('title', 'Edit Tournament | ArenaSync')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="mb-6">
            <a
                href="{{ route('tournaments.show', $tournament) }}"
                class="text-cyan-400 hover:text-cyan-300"
            >
                ← Back to tournament
            </a>
        </div>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-cyan-400">
                Edit Tournament
            </h1>

            <p class="mt-2 text-slate-400">
                Update the tournament category, region, prize type,
                schedule, rules, and participation settings while it is still a draft.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
                <p class="font-semibold">
                    Please fix the following errors:
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

        <div class="rounded-xl border border-slate-800 bg-slate-900 p-6 md:p-8">
            <form
                method="POST"
                action="{{ route('tournaments.update', $tournament) }}"
            >
                @csrf
                @method('PUT')

                @include('tournaments._form', [
                    'tournament' => $tournament,
                    'buttonText' => 'Save Changes',
                    'cancelUrl' => route('tournaments.show', $tournament),
                ])
            </form>
        </div>
    </div>
@endsection