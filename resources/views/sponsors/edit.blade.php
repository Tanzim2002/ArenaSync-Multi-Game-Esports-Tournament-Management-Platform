@extends('layouts.app')

@section('title', 'Edit Sponsor | ArenaSync')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6">
        <a
            href="{{ route('sponsors.show', $sponsor) }}"
            class="text-sm font-medium text-cyan-400 hover:text-cyan-300"
        >
            â† Back to Sponsor
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-cyan-400">
            Edit Sponsor
        </h1>

        <p class="mt-2 text-slate-400">
            {{ $sponsor->name }}
        </p>
    </div>

    <form
        method="POST"
        action="{{ route('sponsors.update', $sponsor) }}"
        class="rounded-xl border border-slate-800 bg-slate-900 p-6"
    >
        @csrf
        @method('PUT')

        @include('sponsors._form')

        <div class="mt-6 flex flex-wrap gap-3">
            <button
                type="submit"
                class="rounded-lg bg-cyan-600 px-6 py-3 font-semibold text-white hover:bg-cyan-500"
            >
                Update Sponsor
            </button>

            <a
                href="{{ route('sponsors.show', $sponsor) }}"
                class="rounded-lg bg-slate-700 px-6 py-3 font-semibold text-white hover:bg-slate-600"
            >
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection