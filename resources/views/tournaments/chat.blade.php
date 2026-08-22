@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold">
            {{ $tournament->title }} - Tournament Chat
        </h1>

        <p class="text-sm text-gray-400 mt-1">
            Chat with tournament participants and view organizer announcements.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-900 text-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-900 text-red-200">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Organizer Announcement Form --}}
    @if ((int) auth()->id() === (int) $tournament->organizer_id)
        <div class="mb-6 border border-yellow-700 rounded-lg p-4">
            <h2 class="font-semibold text-lg mb-3">
                Post Announcement
            </h2>

            <form
                method="POST"
                action="{{ route('tournaments.announcements.store', $tournament) }}"
            >
                @csrf

                <textarea
                    name="message"
                    rows="3"
                    required
                    maxlength="2000"
                    class="w-full rounded bg-slate-900 border border-slate-700 p-3"
                    placeholder="Write an announcement..."
                >{{ old('message') }}</textarea>

                <button
                    type="submit"
                    class="mt-3 px-4 py-2 rounded bg-yellow-600 hover:bg-yellow-500 text-white"
                >
                    Publish Announcement
                </button>
            </form>
        </div>
    @endif

    {{-- Chat Messages --}}
    <div class="border border-slate-700 rounded-lg p-4 mb-6">

        <h2 class="font-semibold text-lg mb-4">
            Messages
        </h2>

        @forelse ($messages as $message)

            <div
                class="mb-4 p-3 rounded
                {{ $message->isAnnouncement()
                    ? 'bg-yellow-950 border border-yellow-700'
                    : 'bg-slate-900' }}"
            >

                <div class="flex justify-between items-center mb-2">

                    <div>
                        <span class="font-semibold">
                            {{ $message->user->name }}
                        </span>

                        @if ($message->isAnnouncement())
                            <span class="ml-2 text-xs font-bold text-yellow-400">
                                ANNOUNCEMENT
                            </span>
                        @endif
                    </div>

                    <span class="text-xs text-gray-400">
                        {{ $message->created_at->format('d M Y, h:i A') }}
                    </span>

                </div>

                <p class="whitespace-pre-line">
                    {{ $message->message }}
                </p>

            </div>

        @empty

            <p class="text-gray-400">
                No messages yet.
            </p>

        @endforelse

    </div>

    {{-- Chat Form --}}
    <div class="border border-slate-700 rounded-lg p-4">

        <h2 class="font-semibold text-lg mb-3">
            Send Message
        </h2>

        <form
            method="POST"
            action="{{ route('tournaments.chat.store', $tournament) }}"
        >
            @csrf

            <textarea
                name="message"
                rows="3"
                required
                maxlength="2000"
                class="w-full rounded bg-slate-900 border border-slate-700 p-3"
                placeholder="Write a message..."
            ></textarea>

            <button
                type="submit"
                class="mt-3 px-4 py-2 rounded bg-cyan-600 hover:bg-cyan-500 text-white"
            >
                Send Message
            </button>

        </form>

    </div>

    <div class="mt-6">
        {{ $messages->links() }}
    </div>

</div>
@endsection
