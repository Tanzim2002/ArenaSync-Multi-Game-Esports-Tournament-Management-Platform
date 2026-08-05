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
                    <span class="text-sm text-slate-300">
                        {{ auth()->user()->name }}
                        ({{ auth()->user()->role }})
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