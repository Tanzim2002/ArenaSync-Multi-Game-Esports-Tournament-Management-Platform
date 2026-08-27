<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Result Verification - ArenaSync</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(0, 190, 240, 0.08), transparent 32%),
                #07111f;
            color: #eaf6ff;
        }

        .topbar {
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 42px;
            border-bottom: 1px solid #1d3248;
            background: #081523;
        }

        .brand {
            color: #18c7f4;
            font-size: 23px;
            font-weight: 700;
        }

        .user-area {
            color: #a9bccb;
            font-size: 14px;
        }

        .page {
            width: min(1180px, calc(100% - 40px));
            margin: 0 auto;
            padding: 40px 0 60px;
        }

        .back-link {
            display: inline-block;
            color: #76d1f7;
            text-decoration: none;
            margin-bottom: 22px;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            margin-bottom: 26px;
        }

        .heading h1 {
            margin: 0 0 7px;
            font-size: 29px;
        }

        .heading p {
            margin: 0;
            color: #899fb2;
            line-height: 1.5;
        }

        .summary-badge {
            background: #0d1d2c;
            border: 1px solid #284056;
            border-radius: 9px;
            padding: 11px 15px;
            color: #9bdff7;
            white-space: nowrap;
            font-size: 13px;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 22px;
            padding: 14px 17px;
            font-size: 14px;
        }

        .alert-success {
            color: #9ff5c1;
            background: rgba(34, 197, 94, 0.09);
            border: 1px solid rgba(34, 197, 94, 0.35);
        }

        .alert-error {
            color: #ffb6b6;
            background: rgba(239, 68, 68, 0.09);
            border: 1px solid rgba(239, 68, 68, 0.35);
        }

        .result-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .result-card {
            background: #0d1b2b;
            border: 1px solid #20384d;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 15px 38px rgba(0, 0, 0, 0.18);
        }

        .result-card-header {
            padding: 18px 22px;
            border-bottom: 1px solid #20384d;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .match-title {
            margin: 0 0 5px;
            font-size: 18px;
            font-weight: 700;
        }

        .match-meta {
            color: #8298a9;
            font-size: 13px;
        }

        .status {
            display: inline-flex;
            align-items: center;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .4px;
        }

        .status-pending {
            color: #ffd27c;
            background: rgba(245, 158, 11, 0.10);
            border: 1px solid rgba(245, 158, 11, 0.38);
        }

        .status-verified {
            color: #91f2b8;
            background: rgba(34, 197, 94, 0.10);
            border: 1px solid rgba(34, 197, 94, 0.38);
        }

        .status-rejected {
            color: #ffaaa9;
            background: rgba(239, 68, 68, 0.10);
            border: 1px solid rgba(239, 68, 68, 0.38);
        }

        .result-body {
            padding: 25px 22px;
        }

        .score-area {
            display: grid;
            grid-template-columns: 1fr 80px 1fr;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .team-box {
            background: #101f30;
            border: 1px solid #263f55;
            border-radius: 9px;
            padding: 19px;
            text-align: center;
        }

        .team-name {
            color: #cbd9e4;
            font-size: 15px;
            margin-bottom: 10px;
        }

        .score {
            color: #ffffff;
            font-size: 32px;
            line-height: 1;
            font-weight: 700;
        }

        .winner {
            display: inline-block;
            margin-top: 10px;
            color: #7fe5ad;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .vs {
            text-align: center;
            color: #71889b;
            font-size: 15px;
            font-weight: 700;
        }

        .details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 13px;
            margin-bottom: 20px;
        }

        .detail {
            background: #091725;
            border: 1px solid #1d3549;
            border-radius: 8px;
            padding: 13px 14px;
        }

        .detail-label {
            color: #748c9f;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: .5px;
        }

        .detail-value {
            color: #dce8f0;
            font-size: 13px;
            word-break: break-word;
        }

        .remarks {
            margin-bottom: 20px;
            padding: 15px;
            background: #091725;
            border: 1px solid #1d3549;
            border-radius: 8px;
        }

        .remarks-title {
            font-size: 12px;
            color: #8198ab;
            margin-bottom: 7px;
            font-weight: 700;
        }

        .remarks-text {
            color: #cad7e1;
            font-size: 13px;
            line-height: 1.6;
        }

        .review-section {
            border-top: 1px solid #20384d;
            padding-top: 20px;
        }

        .review-title {
            margin: 0 0 14px;
            font-size: 15px;
        }

        .review-note {
            width: 100%;
            min-height: 88px;
            resize: vertical;
            background: #071522;
            border: 1px solid #2a455d;
            border-radius: 7px;
            padding: 12px 13px;
            color: white;
            outline: none;
            font-family: inherit;
            font-size: 13px;
        }

        .review-note:focus {
            border-color: #15bde9;
            box-shadow: 0 0 0 3px rgba(21, 189, 233, 0.08);
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 12px;
        }

        .btn {
            border-radius: 7px;
            padding: 11px 18px;
            border: 0;
            font-weight: 700;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-verify {
            background: #1cc875;
            color: #04160d;
        }

        .btn-verify:hover {
            background: #42dd92;
        }

        .btn-reject {
            background: #de4552;
            color: white;
        }

        .btn-reject:hover {
            background: #ef5965;
        }

        .reviewed-box {
            padding: 14px 16px;
            border-radius: 8px;
            background: #091725;
            border: 1px solid #243c51;
            color: #9db0bf;
            font-size: 13px;
            line-height: 1.6;
        }

        .empty {
            background: #0d1b2b;
            border: 1px dashed #31516a;
            border-radius: 12px;
            text-align: center;
            padding: 55px 25px;
        }

        .empty-icon {
            font-size: 36px;
            margin-bottom: 14px;
        }

        .empty h2 {
            margin: 0 0 8px;
            font-size: 20px;
        }

        .empty p {
            margin: 0;
            color: #8198aa;
        }

        @media (max-width: 760px) {
            .topbar {
                padding: 0 20px;
            }

            .heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .score-area {
                grid-template-columns: 1fr;
            }

            .details {
                grid-template-columns: 1fr;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<header class="topbar">
    <div class="brand">
        ArenaSync
    </div>

    <div class="user-area">
        Organizer · Result Verification
    </div>
</header>

<main class="page">

    <a
        href="{{ route('tournaments.show', $tournament) }}"
        class="back-link"
    >
        ← Back to Tournament
    </a>

    <div class="heading">
        <div>
            <h1>Match Result Verification</h1>

            <p>
                Review submitted match results for
                <strong>{{ $tournament->name }}</strong>
                and verify or reject each submission.
            </p>
        </div>

        <div class="summary-badge">
            {{ $results->count() }}
            {{ $results->count() === 1 ? 'Submission' : 'Submissions' }}
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    @if ($results->isEmpty())

        <section class="empty">
            <div class="empty-icon">✓</div>

            <h2>No result submissions yet</h2>

            <p>
                Submitted match results for this tournament will appear here
                for organizer verification.
            </p>
        </section>

    @else

        <section class="result-list">

            @foreach ($results as $result)

                @php
                    $match = $result->match;
                @endphp

                <article class="result-card">

                    <div class="result-card-header">

                        <div>
                            <div class="match-title">
                                {{ $match?->teamOne?->name ?? 'Team One' }}
                                vs
                                {{ $match?->teamTwo?->name ?? 'Team Two' }}
                            </div>

                            <div class="match-meta">
                                Match #{{ $match?->id ?? '-' }}

                                @if ($match?->round)
                                    · Round {{ $match->round }}
                                @endif
                            </div>
                        </div>

                        @if ($result->isPending())
                            <span class="status status-pending">
                                PENDING
                            </span>

                        @elseif ($result->isVerified())
                            <span class="status status-verified">
                                VERIFIED
                            </span>

                        @elseif ($result->isRejected())
                            <span class="status status-rejected">
                                REJECTED
                            </span>
                        @endif

                    </div>

                    <div class="result-body">

                        <div class="score-area">

                            <div class="team-box">

                                <div class="team-name">
                                    {{ $match?->teamOne?->name ?? 'Team One' }}
                                </div>

                                <div class="score">
                                    {{ $result->team_one_score }}
                                </div>

                                @if (
                                    $result->winner_team_id &&
                                    $result->winner_team_id === $match?->team_one_id
                                )
                                    <div class="winner">
                                        Winner
                                    </div>
                                @endif

                            </div>

                            <div class="vs">
                                VS
                            </div>

                            <div class="team-box">

                                <div class="team-name">
                                    {{ $match?->teamTwo?->name ?? 'Team Two' }}
                                </div>

                                <div class="score">
                                    {{ $result->team_two_score }}
                                </div>

                                @if (
                                    $result->winner_team_id &&
                                    $result->winner_team_id === $match?->team_two_id
                                )
                                    <div class="winner">
                                        Winner
                                    </div>
                                @endif

                            </div>

                        </div>

                        <div class="details">

                            <div class="detail">
                                <div class="detail-label">
                                    Submitted By
                                </div>

                                <div class="detail-value">
                                    {{ $result->submittedBy?->name ?? 'Unknown User' }}
                                </div>
                            </div>

                            <div class="detail">
                                <div class="detail-label">
                                    Submitted At
                                </div>

                                <div class="detail-value">
                                    {{ $result->submitted_at?->format('M d, Y h:i A') ?? '-' }}
                                </div>
                            </div>

                            <div class="detail">
                                <div class="detail-label">
                                    Winner
                                </div>

                                <div class="detail-value">

                                    @if ($result->winnerTeam)
                                        {{ $result->winnerTeam->name }}
                                    @else
                                        Draw
                                    @endif

                                </div>
                            </div>

                        </div>

                        @if ($result->remarks)

                            <div class="remarks">
                                <div class="remarks-title">
                                    Participant Remarks
                                </div>

                                <div class="remarks-text">
                                    {{ $result->remarks }}
                                </div>
                            </div>

                        @endif

                        @if ($result->isPending())

                            <div class="review-section">

                                <h3 class="review-title">
                                    Organizer Decision
                                </h3>

                                <form
                                    method="POST"
                                    action="{{ route('match-results.verify', $result) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <div class="actions">
                                        <button
                                            type="submit"
                                            class="btn btn-verify"
                                        >
                                            ✓ Verify Result
                                        </button>
                                    </div>
                                </form>

                                <form
                                    method="POST"
                                    action="{{ route('match-results.reject', $result) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <textarea
                                        name="review_notes"
                                        class="review-note"
                                        maxlength="2000"
                                        placeholder="Reason for rejection or correction instructions..."
                                    ></textarea>

                                    <div class="actions">
                                        <button
                                            type="submit"
                                            class="btn btn-reject"
                                        >
                                            Reject & Return for Correction
                                        </button>
                                    </div>

                                </form>

                            </div>

                        @else

                            <div class="reviewed-box">

                                @if ($result->isVerified())

                                    This result has been verified.

                                @elseif ($result->isRejected())

                                    <strong>Result rejected.</strong>

                                    @if ($result->review_notes)
                                        <br>
                                        Review note:
                                        {{ $result->review_notes }}
                                    @endif

                                @endif

                                @if ($result->reviewed_at)
                                    <br>
                                    Reviewed:
                                    {{ $result->reviewed_at->format('M d, Y h:i A') }}
                                @endif

                            </div>

                        @endif

                    </div>

                </article>

            @endforeach

        </section>

    @endif

</main>

</body>
</html>
