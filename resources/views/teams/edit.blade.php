<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit {{ $team->name }}</title>
</head>

<body>

    <h1>Edit Team</h1>

    @if (session('success'))
        <p style="color: green;">
            {{ session('success') }}
        </p>
    @endif

    @if ($errors->any())
        <div style="color: red;">
            <strong>Please fix the following problems:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('teams.update', $team) }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        <label for="name">Team Name</label>
        <br>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $team->name) }}"
            required
        >

        <br><br>

        @if ($team->logo)
            <p>Current Logo:</p>

            <img
                src="{{ asset('storage/' . $team->logo) }}"
                alt="{{ $team->name }} logo"
                width="120"
            >

            <br><br>
        @endif

        <label for="logo">New Logo</label>
        <br>

        <input
            type="file"
            id="logo"
            name="logo"
            accept=".jpg,.jpeg,.png,.webp"
        >

        <br><br>

        <label for="preferred_game_id">Preferred Game ID</label>
        <br>

        <input
            type="number"
            id="preferred_game_id"
            name="preferred_game_id"
            value="{{ old('preferred_game_id', $team->preferred_game_id) }}"
            min="1"
        >

        <br><br>

        <label for="description">Description</label>
        <br>

        <textarea
            id="description"
            name="description"
            rows="5"
            cols="40"
        >{{ old('description', $team->description) }}</textarea>

        <br><br>

        <button type="submit">
            Update Team
        </button>
    </form>

    <br>

    <a href="{{ route('teams.show', $team) }}">
        Back to Team Profile
    </a>

</body>
</html>