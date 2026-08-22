<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Tournament;
use App\Models\TournamentMessage;
use App\Models\User;
use Illuminate\Http\Request;

class TournamentMessageController extends Controller
{
    /**
     * Show tournament chat and announcements.
     */
    public function index(Request $request, Tournament $tournament)
    {
        $user = $request->user();

        $this->ensureTournamentAccess($tournament, $user);

        $announcements = TournamentMessage::with('user')
            ->where('tournament_id', $tournament->id)
            ->where('type', TournamentMessage::TYPE_ANNOUNCEMENT)
            ->latest()
            ->get();

        $messages = TournamentMessage::with('user')
            ->where('tournament_id', $tournament->id)
            ->where('type', TournamentMessage::TYPE_CHAT)
            ->orderBy('created_at')
            ->get();

        $canAnnounce = (int) $tournament->organizer_id === (int) $user->id;

        return view('tournaments.chat', compact(
            'tournament',
            'announcements',
            'messages',
            'canAnnounce'
        ));
    }

    /**
     * Store a normal tournament chat message.
     */
    public function storeChat(Request $request, Tournament $tournament)
    {
        $user = $request->user();

        $this->ensureTournamentAccess($tournament, $user);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        TournamentMessage::create([
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
            'type' => TournamentMessage::TYPE_CHAT,
            'message' => $validated['message'],
        ]);

        return redirect()
            ->route('tournaments.chat', $tournament)
            ->with('success', 'Message posted successfully.');
    }

    /**
     * Store an official tournament announcement.
     */
    public function storeAnnouncement(Request $request, Tournament $tournament)
    {
        $user = $request->user();

        $this->ensureOrganizer($tournament, $user);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        TournamentMessage::create([
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
            'type' => TournamentMessage::TYPE_ANNOUNCEMENT,
            'message' => $validated['message'],
        ]);

        return redirect()
            ->route('tournaments.chat', $tournament)
            ->with('success', 'Announcement published successfully.');
    }

    /**
     * Allow tournament organizer or members of an approved team.
     */
    private function ensureTournamentAccess(Tournament $tournament, ?User $user): void
    {
        if (! $user) {
            abort(401);
        }

        if ((int) $tournament->organizer_id === (int) $user->id) {
            return;
        }

        $isApprovedParticipant = Registration::query()
            ->where('tournament_id', $tournament->id)
            ->where('status', Registration::STATUS_APPROVED)
            ->whereHas('team.teamMembers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->exists();

        abort_unless(
            $isApprovedParticipant,
            403,
            'You are not authorized to access this tournament chat.'
        );
    }

    /**
     * Only the tournament organizer may publish announcements.
     */
    private function ensureOrganizer(Tournament $tournament, ?User $user): void
    {
        if (! $user) {
            abort(401);
        }

        abort_unless(
            (int) $tournament->organizer_id === (int) $user->id,
            403,
            'Only the tournament organizer can publish announcements.'
        );
    }
}
