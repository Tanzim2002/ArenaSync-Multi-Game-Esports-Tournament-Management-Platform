<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Submit Match Result - ArenaSync</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(0, 174, 239, 0.10), transparent 35%),
                #07111f;
            color: #eaf6ff;
        }

        .topbar {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 42px;
            border-bottom: 1px solid #1d3248;
            background: #081523;
        }

        .brand {
            font-size: 23px;
            font-weight: 700;
            color: #15c7f4;
        }

        .topbar-user {
            color: #b9c8d6;
            font-size: 14px;
        }

        .page {
            max-width: 1050px;
            margin: 0 auto;
            padding: 42px 25px 60px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 22px;
            color: #75cfff;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .heading {
            margin-bottom: 28px;
        }

        .heading h1 {
            margin: 0 0 8px;
            font-size: 29px;
        }

        .heading p {
            margin: 0;
            color: #8fa4b7;
        }

        .match-card {
            background: #0d1b2b;
            border: 1px solid #20364c;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.25);
        }

        .match-header {
            padding: 22px 26px;
            border-bottom: 1px solid #20364c;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .match-header h2 {
            margin: 0 0 5px;
            font-size: 21px;
        }

        .match-header p {
            margin: 0;
            color: #8198ab;
            font-size: 13px;
        }

        .status {
            padding: 7px 12px;
            border-radius: 20px;
            background: rgba(15, 177, 232, 0.12);
            border: 1px solid #138eb7;
            color: #3ed2ff;
            font-size: 12px;
            font-weight: 700;
        }

        .form-area {
            padding: 30px;
        }

        .teams {
            display: grid;
            grid-template-columns: 1fr 90px 1fr;
            gap: 22px;
            align-items: center;
            margin-bottom: 32px;
        }

        .team {
            background: #101f30;
            border: 1px solid #263d54;
            border-radius: 10px;
            padding: 24px;
            text-align: center;
        }

        .team-name {
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .team-label {
            color: #7f96a9;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .score-input {
            width: 110px;
            padding: 13px;
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: white;
            background: #071420;
            border: 1px solid #34516b;
            border-radius: 8px;
            outline: none;
        }

        .score-input:focus,
        select:focus,
        textarea:focus {
            border-color: #16bdeb;
            box-shadow: 0 0 0 3px rgba(22, 189, 235, 0.10);
        }

        .versus {
            text-align: center;
            color: #71889c;
            font-weight: 700;
            font-size: 16px;
        }

        .field {
            margin-bottom: 24px;
        }

        .field label {
            display: block;
            margin-bottom: 9px;
            color: #c7d4df;
            font-size: 14px;
            font-weight: 600;
        }

        select,
        textarea {
            width: 100%;
            padding: 13px 14px;
            background: #091725;
            border: 1px solid #2b4359;
            border-radius: 7px;
            color: #ecf8ff;
            font-size: 14px;
            outline: none;
        }

        textarea {
            min-height: 115px;
            resize: vertical;
        }

        .notice {
            padding: 14px 16px;
            margin-bottom: 24px;
            border-radius: 7px;
            font-size: 13px;
            line-height: 1.5;
        }

        .notice-info {
            background: rgba(0, 173, 239, 0.08);
            border: 1px solid rgba(0, 173, 239, 0.32);
            color: #9adef5;
        }

        .notice-error {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #ffaaaa;
        }

        .notice-rejected {
            background: rgba(245, 158, 11, 0.09);
            border: 1px solid rgba(245, 158, 11, 0.38);
            color: #ffd28a;
        }

        .error-list {
            margin: 7px 0 0;
            padding-left: 20px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 8px;
        }

        .btn {
            display: inline-block;
            border: none;
            border-radius: 7px;
            padding: 12px 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
        }

        .btn-secondary {
            color: #ccd8e2;
            background: #18283a;
            border: 1px solid #31485e;
        }

        .btn-primary {
            color: #04131d;
            background: #16bfe9;
        }

        .btn-primary:hover {
            background: #40cef0;
        }

        @media (max-width: 720px) {
            .topbar {
                padding: 0 20px;
            }

            .teams {
                grid-template-columns: 1fr;
            }

            .versus {
                padding: 2px 0;
            }

            .actions {
                flex-direction: column-reverse;
            }

            .btn {
                text-align: center;
                width: 100%;
            }
        }
    </style>
</head>

<body>

<header class="topbar">
    <div class="brand">ArenaSync</div>
    <div class="topbar-user">
        Match Result Submission
    </div>
</header>

<main class="page">

    <a href="{{ route('matches.show', $match) }}" class="back-link">
        ← Back to Match Details
    </a>

    <div class="heading">
        <h1>Submit Match Result</h1>
        <p>
            Enter the final score and winning team. The organizer will verify
            the result before it becomes official.
        </p>
    </div>

    @if ($errors->any())
        <div class="notice notice-error">
            <strong>Please correct the following:</strong>

            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($result && $result->isRejected())
        <div class="notice notice-rejected">
            <strong>Previous submission was rejected.</strong>

            @if ($result->review_notes)
                <br>
                Organizer note: {{ $result->review_notes }}
            @endif

            <br>
            Please correct the result and submit it again.
        </div>
    @elseif ($result && $result->isPending())
        <div class="notice notice-info">
            A result has already been submitted for this match and is currently
            waiting for organizer verification. You may update it before it is
            verified.
        </div>
    @endif

    <section class="match-card">

        <div class="match-header">
            <div>
                <h2>{{ $match->teamOne?->name ?? 'Team One' }} vs {{ $match->teamTwo?->name ?? 'Team Two' }}</h2>

                <p>
                    {{ $match->tournament?->name ?? 'Tournament' }}
                    · Round {{ $match->round }}
                </p>
            </div>

            <span class="status">
                RESULT SUBMISSION
            </span>
        </div>

        <form
            method="POST"
            action="{{ route('match-results.store', $match) }}"
            class="form-area"
        >
            @csrf

            <div class="teams">

                <div class="team">
                    <div class="team-label">Team One</div>

                    <div class="team-name">
                        {{ $match->teamOne?->name ?? 'Team One' }}
                    </div>

                    <input
                        type="number"
                        name="team_one_score"
                        class="score-input"
                        min="0"
                        required
                        value="{{ old('team_one_score', $result?->team_one_score ?? 0) }}"
                    >
                </div>

                <div class="versus">
                    VS
                </div>

                <div class="team">
                    <div class="team-label">Team Two</div>

                    <div class="team-name">
                        {{ $match->teamTwo?->name ?? 'Team Two' }}
                    </div>

                    <input
                        type="number"
                        name="team_two_score"
                        class="score-input"
                        min="0"
                        required
                        value="{{ old('team_two_score', $result?->team_two_score ?? 0) }}"
                    >
                </div>

            </div>

            <div class="field">
                <label for="winner_team_id">
                    Winning Team
                </label>

                <select
                    name="winner_team_id"
                    id="winner_team_id"
                >
                    <option value="">
                        Select winner — leave empty for a draw
                    </option>

                    @if ($match->teamOne)
                        <option
                            value="{{ $match->teamOne->id }}"
                            @selected(
                                old(
                                    'winner_team_id',
                                    $result?->winner_team_id
                                ) == $match->teamOne->id
                            )
                        >
                            {{ $match->teamOne->name }}
                        </option>
                    @endif

                    @if ($match->teamTwo)
                        <option
                            value="{{ $match->teamTwo->id }}"
                            @selected(
                                old(
                                    'winner_team_id',
                                    $result?->winner_team_id
                                ) == $match->teamTwo->id
                            )
                        >
                            {{ $match->teamTwo->name }}
                        </option>
                    @endif
                </select>
            </div>

            <div class="field">
                <label for="remarks">
                    Remarks
                </label>

                <textarea
                    name="remarks"
                    id="remarks"
                    maxlength="2000"
                    placeholder="Optional notes about the match result..."
                >{{ old('remarks', $result?->remarks) }}</textarea>
            </div>

            <div class="notice notice-info">
                Once submitted, this result will remain
                <strong>Pending</strong> until it is reviewed by the tournament
                organizer.
            </div>

            <div class="actions">

                <a
                    href="{{ route('matches.show', $match) }}"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    {{ $result ? 'Update Result' : 'Submit for Verification' }}
                </button>

            </div>

        </form>

    </section>

</main>

</body>
</html>
