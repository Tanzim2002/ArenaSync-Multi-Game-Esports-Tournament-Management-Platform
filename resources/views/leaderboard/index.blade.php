@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Leaderboard</h1>

    @if($leaderboard->count() > 0)

        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Team</th>
                    <th>Wins</th>
                </tr>
            </thead>

            <tbody>
                @foreach($leaderboard as $index => $team)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ $team->winnerTeam->name ?? 'Unknown Team' }}
                        </td>
                        <td>
                            {{ $team->wins }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @else

        <p>No leaderboard data available.</p>

    @endif
</div>
@endsection
