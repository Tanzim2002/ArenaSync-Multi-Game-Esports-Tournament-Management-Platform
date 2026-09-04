@extends('layouts.app')

@section('title', 'Livestreams | ArenaSync')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-6">
        <a
            href="{{ route('tournaments.show', $tournament) }}"
            class="text-sm font-medium text-cyan-400 hover:text-cyan-300"
        >
            â† Back to Tournament
        </a>
    </div>

    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-cyan-400">
                Tournament Livestreams
            </h1>

            <p class="mt-2 text-slate-400">
                {{ $tournament->title }}
            </p>
        </div>

        @if ($canManage)
            <a
                href="{{ route('tournaments.livestreams.create', $tournament) }}"
                class="rounded-lg bg-cyan-600 px-5 py-3 font-semibold text-white transition hover:bg-cyan-500"
            >
                Add Livestream
            </a>
        @endif
    </div>

    @if ($livestreams->isEmpty())
        <div class="rounded-xl border border-slate-800 bg-slate-900 p-10 text-center">
            <h2 class="text-xl font-semibold text-white">
                No livestreams available
            </h2>

            <p class="mt-2 text-slate-400">
                @if ($canManage)
                    Add a YouTube, Twitch, or Facebook stream when the broadcast is ready.
                @else
                    The organizer has not published an active livestream yet.
                @endif
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($livestreams as $stream)
                <article class="rounded-xl border border-slate-800 bg-slate-900 p-6">
                    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="rounded-full bg-red-950 px-3 py-1 text-xs font-semibold uppercase text-red-300">
                                    {{ $stream->platform }}
                                </span>

                                @if ($canManage && ! $stream->is_active)
                                    <span class="rounded-full bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-400">
                                        INACTIVE
                                    </span>
                                @endif
                            </div>

                            <h2 class="mt-3 text-xl font-bold text-white">
                                {{ $stream->label ?: 'Live Stream' }}
                            </h2>

                            <p class="mt-1 break-all text-sm text-slate-500">
                                {{ $stream->url }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            @if ($stream->is_active)
                                <a
                                    href="{{ $stream->url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white transition hover:bg-red-500"
                                >
                                    Watch Live
                                </a>
                            @endif

                            @if ($canManage)
                                <a
                                    href="{{ route('tournaments.livestreams.edit', [$tournament, $stream]) }}"
                                    class="rounded-lg bg-amber-600 px-4 py-2 font-semibold text-white transition hover:bg-amber-500"
                                >
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('tournaments.livestreams.destroy', [$tournament, $stream]) }}"
                                    onsubmit="return confirm('Remove this livestream link?');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="rounded-lg bg-slate-700 px-4 py-2 font-semibold text-white transition hover:bg-slate-600"
                                    >
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection