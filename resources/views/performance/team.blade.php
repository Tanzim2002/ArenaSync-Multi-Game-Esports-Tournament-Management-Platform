<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $performance['team']->name }} - Performance History</title>
</head>

<body>

    <h1>{{ $performance['team']->name }} - Performance History</h1>

    <p>
        <a href="{{ route('teams.show', $performance['team']) }}">
            Back to Team Profile
        </a>
    </p>

    <hr>

    <h2>Performance Summary</h2>

    <p>
        <strong>Matches Played:</strong>
        {{ $performance['summary']['matches_played'] }}
    </p>

    <p>
        <strong>Wins:</strong>
        {{ $performance['summary']['wins'] }}
    </p>

    <p>
        <strong>Losses:</strong>
        {{ $performance['summary']['losses'] }}
    </p>

    <p>
        <strong>Draws:</strong>
        {{ $performance['summary']['draws'] }}
    </p>

    <p>
        <strong>Score For:</strong>
        {{ $performance['summary']['score_for'] }}
    </p>

    <p>
        <strong>Score Against:</strong>
        {{ $performance['summary']['score_against'] }}
    </p>

    <p>
        <strong>Win Rate:</strong>
        {{ $performance['summary']['win_rate'] }}%
    </p>

    <hr>

    <h2>Verified Match History</h2>

    @if (empty($performance['history']))

        <p>No verified match history is available for this team yet.</p>

    @else

        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Tournament</th>
                    <th>Round</th>
                    <th>Opponent</th>
                    <th>Score</th>
                    <th>Result</th>
                    <th>Match Date</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($performance['history'] as $match)
                    <tr>
                        <td>
                            {{ $match['tournament'] }}
                        </td>

                        <td>
                            {{ $match['round'] }}
                        </td>

                        <td>
                            {{ $match['opponent'] }}
                        </td>

                        <td>
                            {{ $match['team_score'] }}
                            -
                            {{ $match['opponent_score'] }}
                        </td>

                        <td>
                            {{ $match['outcome'] }}
                        </td>

                        <td>
                            {{ $match['scheduled_at']
                                ? $match['scheduled_at']->format('d M Y, h:i A')
                                : 'Not available' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif

</body>

</html>