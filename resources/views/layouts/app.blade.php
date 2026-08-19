<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'ArenaSync')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 text-slate-100">
    <nav class="border-b border-slate-800 bg-slate-900">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <a href="{{ url('/') }}" class="text-xl font-bold text-cyan-400">
                ArenaSync
            </a>

            <div class="flex items-center gap-4">
                @auth
                    @if (auth()->user()->hasRole(\App\Models\User::ROLE_ORGANIZER))
                        <a
                            href="{{ route('organizer-verification.show') }}"
                            class="text-sm hover:text-cyan-400"
                        >
                            Verification
                        </a>
                    @endif

                    @if (auth()->user()->hasRole(\App\Models\User::ROLE_ADMIN))
                        <a
                            href="{{ route('admin.organizer-verifications.index') }}"
                            class="text-sm hover:text-cyan-400"
                        >
                            Organizer Verifications
                        </a>
                    @endif

                    <span class="flex items-center gap-2 text-sm text-slate-300">
                        <span>
                            {{ auth()->user()->name }}
                            ({{ auth()->user()->role }})
                        </span>

                        @if (
                            auth()->user()->hasRole(\App\Models\User::ROLE_ORGANIZER)
                            && auth()->user()->organizerVerification?->isVerified()
                        )
                            <span class="rounded-full border border-emerald-500 bg-emerald-950 px-2 py-1 text-xs font-semibold text-emerald-300">
                                ✓ Verified
                            </span>
                        @endif
                    </span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="rounded bg-red-600 px-4 py-2 text-sm hover:bg-red-500"
                        >
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-cyan-400">
                        Login
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="rounded bg-cyan-600 px-4 py-2 hover:bg-cyan-500"
                    >
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <a href="{{ route('games.index') }}" class="hover:text-cyan-400">
        Games
    </a>

    <main class="mx-auto max-w-6xl px-6 py-10">
        @if (session('success'))
            <div class="mb-6 rounded border border-green-500 bg-green-950 p-4 text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
