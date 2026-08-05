<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $team->name }} - Team Profile</title>
</head>

<body>

    <h1>{{ $team->name }}</h1>

    @if (session('success'))
        <p style="color: green;">
            {{ session('success') }}
        </p>
    @endif

    @if ($team->logo)
        <div>
            <img
                src="{{ asset('storage/' . $team->logo) }}"
                alt="{{ $team->name }} logo"
                width="150"
            >
        </div>

        <br>
    @endif

    <p>
        <strong>Team Leader:</strong>
        {{ $team->leader->name }}
    </p>

    <p>
        <strong>Preferred Game ID:</strong>
        {{ $team->preferred_game_id ?? 'Not selected' }}
    </p>

    <p>
        <strong>Description:</strong>
        {{ $team->description ?? 'No description added.' }}
    </p>

    <p>
        <strong>Created:</strong>
        {{ $team->created_at->format('d M Y') }}
    </p>

    @if ($team->leader_id === auth()->id())
        <a href="{{ route('teams.edit', $team) }}">
            Edit Team
        </a>
    @endif

    <br><br>

    <a href="{{ route('teams.create') }}">
        Create Another Team
    </a>

</body>
</html>