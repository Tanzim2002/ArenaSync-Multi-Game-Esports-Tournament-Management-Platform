@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Livestreams — {{ $tournament->title }}</h3>
        @auth
            @if (auth()->id() === $tournament->organizer_id || auth()->user()->role === 'admin')
                <a href="{{ route('tournaments.livestreams.create', $tournament) }}" class="btn btn-sm btn-primary">
                    + Add Livestream
                </a>
            @endif
        @endauth
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @forelse ($livestreams as $stream)
        <div class="card mb-2">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-secondary text-uppercase">{{ $stream->platform }}</span>
                    <strong class="ms-2">{{ $stream->label ?? 'Live Stream' }}</strong>
                </div>
                <div>
                    <a href="{{ $stream->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-danger">▶ Watch Live</a>
                    @auth
                        @if (auth()->id() === $tournament->organizer_id || auth()->user()->role === 'admin')
                            <a href="{{ route('tournaments.livestreams.edit', [$tournament, $stream]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="{{ route('tournaments.livestreams.destroy', [$tournament, $stream]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this stream link?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">No livestream links added yet.</p>
    @endforelse
</div>
@endsection