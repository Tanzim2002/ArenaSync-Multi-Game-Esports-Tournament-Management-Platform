<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $performance['player']->name }} - Performance History</title>
</head>

<body>

    <h1>{{ $performance['player']->name }} - Performance History</h1>

    <p>
        This performance is based on the verified match results
        of the teams connected to this player.
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

    <h2>Teams</h2>

    @if ($performance['team_performances']->isEmpty())

        <p>No team performance history is available for this player yet.</p>

    @else

        @foreach ($performance['team_performances'] as $teamPerformance)

            <h3>
                {{ $teamPerformance['team']->name }}
            </h3>

            <p>
                <strong>Matches Played:</strong>
                {{ $teamPerformance['summary']['matches_played'] }}
            </p>

            <p>
                <strong>Wins:</strong>
                {{ $teamPerformance['summary']['wins'] }}
            </p>

            <p>
                <strong>Losses:</strong>
                {{ $teamPerformance['summary']['losses'] }}
            </p>

            <p>
                <strong>Draws:</strong>
                {{ $teamPerformance['summary']['draws'] }}
            </p>

            <p>
                <strong>Win Rate:</strong>
                {{ $teamPerformance['summary']['win_rate'] }}%
            </p>

            <p>
                <a href="{{ route(
                    'performance.teams.show',
                    $teamPerformance['team']
                ) }}">
                    View Team Performance History
                </a>
            </p>

            <hr>

        @endforeach

    @endif

</body>

</html>