@extends('layouts.app')

@section('title', 'Add Sponsor | ArenaSync')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <a
            href="{{ route('sponsors.index') }}"
            class="text-sm font-medium text-cyan-400 hover:text-cyan-300"
        >
            â† Back to Sponsors
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-cyan-400">
            Add Sponsor
        </h1>

        <p class="mt-2 text-slate-400">
            Attach a sponsorship record to a tournament you manage.
        </p>
    </div>

    <form
        method="POST"
        action="{{ route('sponsors.store') }}"
        class="rounded-xl border border-slate-800 bg-slate-900 p-6"
    >
        @csrf

        @include('sponsors._form')

        <div class="mt-6 flex flex-wrap gap-3">
            <button
                type="submit"
                class="rounded-lg bg-cyan-600 px-6 py-3 font-semibold text-white hover:bg-cyan-500"
            >
                Create Sponsor
            </button>

            <a
                href="{{ route('sponsors.index') }}"
                class="rounded-lg bg-slate-700 px-6 py-3 font-semibold text-white hover:bg-slate-600"
            >
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection