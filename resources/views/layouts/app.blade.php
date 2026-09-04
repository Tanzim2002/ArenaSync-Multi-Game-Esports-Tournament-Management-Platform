<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'ArenaSync')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 text-slate-100">
    <nav class="border-b border-slate-800 bg-slate-900/95">
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <a
                    href="{{ route('home') }}"
                    class="text-xl font-black tracking-tight text-cyan-400"
                >
                    ArenaSync
                </a>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-3 text-sm">
                    <a
                        href="{{ route('tournaments.index') }}"
                        class="font-medium text-slate-300 transition hover:text-cyan-400"
                    >
                        Tournaments
                    </a>

                    <a
                        href="{{ route('games.index') }}"
                        class="font-medium text-slate-300 transition hover:text-cyan-400"
                    >
                        Games
                    </a>

                    <a
                        href="{{ route('leaderboard.index') }}"
                        class="font-medium text-slate-300 transition hover:text-cyan-400"
                    >
                        Leaderboard
                    </a>

                    <a
                        href="{{ route('sponsors.index') }}"
                        class="font-medium text-slate-300 transition hover:text-cyan-400"
                    >
                        Sponsors
                    </a>

                    @auth
                        <a
                            href="{{ route('dashboard.index') }}"
                            class="font-medium text-slate-300 transition hover:text-cyan-400"
                        >
                            Dashboard
                        </a>

                        @if (auth()->user()->hasRole(\App\Models\User::ROLE_PLAYER))
                            <a
                                href="{{ route('teams.create') }}"
                                class="font-medium text-slate-300 transition hover:text-cyan-400"
                            >
                                Create Team
                            </a>
                        @endif

                        @if (auth()->user()->hasRole(\App\Models\User::ROLE_ORGANIZER))
                            <a
                                href="{{ route('tournaments.manage') }}"
                                class="font-medium text-slate-300 transition hover:text-cyan-400"
                            >
                                Manage Tournaments
                            </a>

                            <a
                                href="{{ route('organizer-verification.show') }}"
                                class="font-medium text-slate-300 transition hover:text-cyan-400"
                            >
                                Verification
                            </a>
                        @endif

                        @if (auth()->user()->hasRole(\App\Models\User::ROLE_ADMIN))
                            <a
                                href="{{ route('admin.organizer-verifications.index') }}"
                                class="font-medium text-slate-300 transition hover:text-cyan-400"
                            >
                                Organizer Reviews
                            </a>
                        @endif
                    @endauth
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @auth
                        <div class="flex items-center gap-2 rounded-full border border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-300">
                            <span class="font-semibold text-white">
                                {{ auth()->user()->name }}
                            </span>

                            <span class="text-slate-500">&middot;</span>

                            <span>
                                {{ auth()->user()->role }}
                            </span>

                            @if (
                                auth()->user()->hasRole(\App\Models\User::ROLE_ORGANIZER)
                                && auth()->user()->organizerVerification?->isVerified()
                            )
                                <span class="rounded-full bg-emerald-950 px-2 py-0.5 font-semibold text-emerald-300">
                                    {{ "\u{2713} Verified" }}
                                </span>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-500"
                            >
                                Logout
                            </button>
                        </form>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="rounded-lg border border-slate-700 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-cyan-600 hover:text-cyan-300"
                        >
                            Login
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-500"
                        >
                            Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-emerald-700 bg-emerald-950/50 p-4 text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('status'))
            <div class="mb-6 rounded-lg border border-cyan-700 bg-cyan-950/50 p-4 text-cyan-200">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-slate-900 py-8 text-center text-sm text-slate-500">
        ArenaSync &middot; Multi-Game Esports Tournament Management Platform
    </footer>
</body>
</html>