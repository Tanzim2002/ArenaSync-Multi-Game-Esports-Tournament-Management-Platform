<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display the dummy payment submission form for an approved registration.
     */
    public function create(Request $request, Registration $registration): View
    {
        $this->ensureTeamLeaderOwnsRegistration($request, $registration);

        $registration->load('tournament');

        return view('payments.create', compact('registration'));
    }

    /**
     * Submit a dummy payment for an approved registration.
     */
    public function store(StorePaymentRequest $request, Registration $registration): RedirectResponse
    {
        $this->ensureTeamLeaderOwnsRegistration($request, $registration);

        $tournament = $registration->tournament;

        if (! $tournament->is_paid) {
            return back()->withErrors([
                'payment' => 'This tournament is free. No payment submission is required.',
            ]);
        }

        if (! $registration->isApproved()) {
            return back()->withErrors([
                'payment' => 'Only an approved registration may submit payment.',
            ]);
        }

        if ($registration->payment()->exists()) {
            return back()->withErrors([
                'payment' => 'A payment has already been submitted for this registration.',
            ]);
        }

        $data = $request->validated();
        $data['status'] = Payment::STATUS_PENDING;
        $data['submitted_at'] = now();

        $registration->payment()->create($data);

        return redirect()
            ->route('registrations.payment.show', $registration)
            ->with('success', 'Payment submitted successfully. Awaiting organizer verification.');
    }

    /**
     * Display the payment status for a registration owned by the team leader.
     */
    public function show(Request $request, Registration $registration): View
    {
        $this->ensureTeamLeaderOwnsRegistration($request, $registration);

        $registration->load(['tournament', 'payment']);

        return view('payments.show', compact('registration'));
    }

    /**
     * Resolve the authenticated team leader's registration for a tournament
     * and redirect them to its payment page. Used as the single link-in point
     * from the tournament show page.
     */
    public function mine(Request $request, Tournament $tournament): RedirectResponse
    {
        $registration = Registration::query()
            ->where('tournament_id', $tournament->id)
            ->whereHas('team', fn ($query) => $query->where('leader_id', $request->user()->id))
            ->latest('submitted_at')
            ->first();

        if ($registration === null) {
            return back()->withErrors([
                'payment' => 'You do not have a registration for this tournament yet.',
            ]);
        }

        return redirect()->route('registrations.payment.show', $registration);
    }

    /**
     * Display payments submitted for an organizer-owned tournament.
     */
    public function index(Request $request, Tournament $tournament): View
    {
        $this->ensureOrganizerOwnsTournament($request, $tournament);

        $payments = Payment::query()
            ->whereHas('registration', fn ($query) => $query->where('tournament_id', $tournament->id))
            ->with(['registration.team.leader'])
            ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', [Payment::STATUS_PENDING])
            ->orderByDesc('submitted_at')
            ->paginate(15);

        return view('payments.index', compact('tournament', 'payments'));
    }

    /**
     * Verify a pending payment.
     */
    public function verify(Request $request, Tournament $tournament, Payment $payment): RedirectResponse
    {
        $this->ensureOrganizerOwnsTournament($request, $tournament);
        $this->ensurePaymentBelongsToTournament($payment, $tournament);

        if (! $payment->isPending()) {
            return back()->withErrors([
                'payment' => 'Only pending payments can be verified.',
            ]);
        }

        $payment->update([
            'status' => Payment::STATUS_VERIFIED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Payment verified successfully.');
    }

    /**
     * Reject a pending payment.
     */
    public function reject(Request $request, Tournament $tournament, Payment $payment): RedirectResponse
    {
        $this->ensureOrganizerOwnsTournament($request, $tournament);
        $this->ensurePaymentBelongsToTournament($payment, $tournament);

        if (! $payment->isPending()) {
            return back()->withErrors([
                'payment' => 'Only pending payments can be rejected.',
            ]);
        }

        $payment->update([
            'status' => Payment::STATUS_REJECTED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Payment rejected successfully.');
    }

    /**
     * Ensure the authenticated user leads the team that owns this registration.
     */
    private function ensureTeamLeaderOwnsRegistration(Request $request, Registration $registration): void
    {
        $user = $request->user();

        $registration->loadMissing('team');

        abort_unless(
            $user !== null && $registration->team->leader_id === $user->id,
            403
        );
    }

    /**
     * Ensure the authenticated user is the organizer who owns this tournament.
     */
    private function ensureOrganizerOwnsTournament(Request $request, Tournament $tournament): void
    {
        $user = $request->user();

        abort_unless(
            $user !== null
            && $user->hasRole(User::ROLE_ORGANIZER)
            && $tournament->organizer_id === $user->id,
            403
        );
    }

    /**
     * Ensure the payment's registration belongs to the supplied tournament.
     */
    private function ensurePaymentBelongsToTournament(Payment $payment, Tournament $tournament): void
    {
        $payment->loadMissing('registration');

        abort_unless($payment->registration->tournament_id === $tournament->id, 404);
    }
}