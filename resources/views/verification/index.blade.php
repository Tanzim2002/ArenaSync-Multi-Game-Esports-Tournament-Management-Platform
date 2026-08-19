@extends('layouts.app')

@section('title', 'Organizer Verifications | ArenaSync')

@section('content')
    <div class="mx-auto max-w-6xl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-cyan-400">
                Organizer Verification Requests
            </h1>

            <p class="mt-2 text-slate-400">
                Review organizer verification requests and approve or reject pending submissions.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-700 bg-red-950/50 p-4 text-red-200">
                <ul class="list-inside list-disc space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="overflow-x-auto rounded-xl border border-slate-800">
            <table class="min-w-full divide-y divide-slate-800 bg-slate-900">
                <thead class="bg-slate-950">
                    <tr class="text-left text-sm text-slate-400">
                        <th class="px-5 py-4">
                            Organizer
                        </th>

                        <th class="px-5 py-4">
                            Status
                        </th>

                        <th class="px-5 py-4">
                            Requested
                        </th>

                        <th class="px-5 py-4">
                            Reviewed By
                        </th>

                        <th class="px-5 py-4">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-800">
                    @forelse ($verifications as $verification)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-white">
                                    {{ $verification->organizer->name }}
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $verification->organizer->email }}
                                </p>
                            </td>

                            <td class="px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold
                                    @if ($verification->isVerified())
                                        bg-emerald-950 text-emerald-300
                                    @elseif ($verification->isRejected())
                                        bg-red-950 text-red-300
                                    @else
                                        bg-amber-950 text-amber-300
                                    @endif
                                ">
                                    {{ $verification->status }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-300">
                                {{ $verification->requested_at->format('M d, Y h:i A') }}
                            </td>

                            <td class="px-5 py-4 text-sm text-slate-300">
                                {{ $verification->reviewer?->name ?? '—' }}
                            </td>

                            <td class="px-5 py-4">
                                @if ($verification->isPending())
                                    <div class="flex flex-wrap gap-2">
                                        <form
                                            method="POST"
                                            action="{{ route('admin.organizer-verifications.approve', $verification) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="rounded bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500"
                                            >
                                                Approve
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.organizer-verifications.reject', $verification) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="rounded bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500"
                                            >
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-sm text-slate-500">
                                        Reviewed
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="px-5 py-10 text-center text-slate-500"
                            >
                                No organizer verification requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $verifications->links() }}
        </div>
    </div>
@endsection
