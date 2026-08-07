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

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
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


    <hr>

    <h2>Team Members</h2>

    @if ($team->teamMembers->isEmpty())
        <p>No players have joined this team yet.</p>
    @else
        <ul>
            @foreach ($team->teamMembers as $member)
                <li>
                    {{ $member->user->name }}
                    ({{ $member->user->email }})
                </li>
            @endforeach
        </ul>
    @endif


    <hr>


    @if ($team->leader_id === auth()->id())

        <h2>Invite Player</h2>

        <form
            method="POST"
            action="{{ route('teams.invitations.store', $team) }}"
        >
            @csrf

            <label for="email">Player Email:</label>

            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
            >

            <button type="submit">
                Send Invitation
            </button>
        </form>


        <h2>Pending Membership Requests</h2>

        @if ($pendingRequests->isEmpty())
            <p>No pending requests.</p>
        @else

            @foreach ($pendingRequests as $membershipRequest)

                <div style="margin-bottom: 20px;">

                    <p>
                        <strong>Player:</strong>
                        {{ $membershipRequest->user->name }}
                        ({{ $membershipRequest->user->email }})
                    </p>

                    <p>
                        <strong>Type:</strong>
                        {{ $membershipRequest->request_type }}
                    </p>


                    @if ($membershipRequest->request_type === 'JOIN_REQUEST')

                        <form
                            method="POST"
                            action="{{ route('team-membership-requests.accept', $membershipRequest) }}"
                            style="display: inline;"
                        >
                            @csrf
                            @method('PATCH')

                            <button type="submit">
                                Accept
                            </button>
                        </form>


                        <form
                            method="POST"
                            action="{{ route('team-membership-requests.reject', $membershipRequest) }}"
                            style="display: inline;"
                        >
                            @csrf
                            @method('PATCH')

                            <button type="submit">
                                Reject
                            </button>
                        </form>

                    @else

                        <p>Invitation waiting for player response.</p>

                        <form
                            method="POST"
                            action="{{ route('team-membership-requests.cancel', $membershipRequest) }}"
                        >
                            @csrf
                            @method('PATCH')

                            <button type="submit">
                                Cancel Invitation
                            </button>
                        </form>

                    @endif

                </div>

            @endforeach

        @endif


    @else

        @php
            $isMember = $team->teamMembers
                ->contains('user_id', auth()->id());

            $myPendingRequest = $pendingRequests
                ->firstWhere('user_id', auth()->id());
        @endphp


        @if ($isMember)

            <h2>Membership</h2>

            <p>
                You are a member of this team.
            </p>


        @elseif ($myPendingRequest)

            @if ($myPendingRequest->request_type === 'INVITATION')

                <h2>Team Invitation</h2>

                <p>
                    You have been invited to join this team.
                </p>

                <form
                    method="POST"
                    action="{{ route('team-membership-requests.accept', $myPendingRequest) }}"
                    style="display: inline;"
                >
                    @csrf
                    @method('PATCH')

                    <button type="submit">
                        Accept Invitation
                    </button>
                </form>


                <form
                    method="POST"
                    action="{{ route('team-membership-requests.reject', $myPendingRequest) }}"
                    style="display: inline;"
                >
                    @csrf
                    @method('PATCH')

                    <button type="submit">
                        Reject Invitation
                    </button>
                </form>


            @else

                <h2>Join Request</h2>

                <p>
                    Your request to join this team is pending.
                </p>

                <form
                    method="POST"
                    action="{{ route('team-membership-requests.cancel', $myPendingRequest) }}"
                >
                    @csrf
                    @method('PATCH')

                    <button type="submit">
                        Cancel Join Request
                    </button>
                </form>

            @endif


        @else

            <h2>Join Team</h2>

            <form
                method="POST"
                action="{{ route('teams.join-requests.store', $team) }}"
            >
                @csrf

                <button type="submit">
                    Request to Join
                </button>
            </form>

        @endif

    @endif


    <br><br>


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