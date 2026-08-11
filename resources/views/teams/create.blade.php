<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Team</title>
</head>

<body>

    <h1>Create Team</h1>

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
        action="{{ route('teams.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        <label for="name">Team Name</label>
        <br>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name') }}"
            required
        >

        <br><br>

        <label for="logo">Logo</label>
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
            value="{{ old('preferred_game_id') }}"
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
        >{{ old('description') }}</textarea>

        <br><br>

        <button type="submit">
            Create Team
        </button>
    </form>

</body>
</html>