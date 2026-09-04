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
    public function create(
        Request $request,
        Registration $registration
    ): View {
        $this->ensureTeamLeaderOwnsRegistration(
            $request,
            $registration
        );

        $registration->load([
            'tournament',
            'payment',
        ]);

        return view(
            'payments.create',
            compact('registration')
        );
    }

    public function store(
        StorePaymentRequest $request,
        Registration $registration
    ): RedirectResponse {
        $this->ensureTeamLeaderOwnsRegistration(
            $request,
            $registration
        );

        $registration->loadMissing('tournament');

        $tournament = $registration->tournament;

        if (! $tournament->requiresPayment()) {
            return back()->withErrors([
                'payment'
                    => 'This tournament is free. No payment submission is required.',
            ]);
        }

        if (! $registration->isApproved()) {
            return back()->withErrors([
                'payment'
                    => 'Only an approved registration may submit payment.',
            ]);
        }

        if ($registration->payment()->exists()) {
            return back()->withErrors([
                'payment'
                    => 'A payment has already been submitted for this registration.',
            ]);
        }

        $data = $request->validated();

        if (
            (float) $data['amount']
            !==
            (float) $tournament->entry_fee
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'amount'
                        => 'The payment amount must match the tournament entry fee.',
                ]);
        }

        $registration->payment()->create([
            'amount' => $data['amount'],

            'method' => $data['method'],

            'reference' => $data['reference'],

            'status' => Payment::STATUS_PENDING,

            'submitted_at' => now(),
        ]);

        return redirect()
            ->route(
                'registrations.payment.show',
                $registration
            )
            ->with(
                'success',
                'Payment submitted successfully. Awaiting organizer verification.'
            );
    }

    public function show(
        Request $request,
        Registration $registration
    ): View {
        $this->ensureTeamLeaderOwnsRegistration(
            $request,
            $registration
        );

        $registration->load([
            'tournament',
            'payment',
        ]);

        return view(
            'payments.show',
            compact('registration')
        );
    }

    public function mine(
        Request $request,
        Tournament $tournament
    ): RedirectResponse {
        $registration = Registration::query()
            ->where(
                'tournament_id',
                $tournament->id
            )
            ->whereHas(
                'team',
                fn ($query) =>
                    $query->where(
                        'leader_id',
                        $request->user()->id
                    )
            )
            ->latest('submitted_at')
            ->first();

        if ($registration === null) {
            return back()->withErrors([
                'payment'
                    => 'You do not have a registration for this tournament yet.',
            ]);
        }

        return redirect()->route(
            'registrations.payment.show',
            $registration
        );
    }

    public function index(
        Request $request,
        Tournament $tournament
    ): View {
        $this->ensureOrganizerOwnsTournament(
            $request,
            $tournament
        );

        $payments = Payment::query()
            ->whereHas(
                'registration',
                fn ($query) =>
                    $query->where(
                        'tournament_id',
                        $tournament->id
                    )
            )
            ->with([
                'registration.team.leader',
            ])
            ->orderByRaw(
                'CASE WHEN status = ? THEN 0 ELSE 1 END',
                [
                    Payment::STATUS_PENDING,
                ]
            )
            ->orderByDesc('submitted_at')
            ->paginate(15);

        return view(
            'payments.index',
            compact(
                'tournament',
                'payments'
            )
        );
    }

    public function verify(
        Request $request,
        Tournament $tournament,
        Payment $payment
    ): RedirectResponse {
        $this->ensureOrganizerOwnsTournament(
            $request,
            $tournament
        );

        $this->ensurePaymentBelongsToTournament(
            $payment,
            $tournament
        );

        if (! $payment->isPending()) {
            return back()->withErrors([
                'payment'
                    => 'Only pending payments can be verified.',
            ]);
        }

        $payment->update([
            'status'
                => Payment::STATUS_VERIFIED,

            'reviewed_by'
                => $request->user()->id,

            'reviewed_at'
                => now(),
        ]);

        return back()->with(
            'success',
            'Payment verified successfully.'
        );
    }

    public function reject(
        Request $request,
        Tournament $tournament,
        Payment $payment
    ): RedirectResponse {
        $this->ensureOrganizerOwnsTournament(
            $request,
            $tournament
        );

        $this->ensurePaymentBelongsToTournament(
            $payment,
            $tournament
        );

        if (! $payment->isPending()) {
            return back()->withErrors([
                'payment'
                    => 'Only pending payments can be rejected.',
            ]);
        }

        $payment->update([
            'status'
                => Payment::STATUS_REJECTED,

            'reviewed_by'
                => $request->user()->id,

            'reviewed_at'
                => now(),
        ]);

        return back()->with(
            'success',
            'Payment rejected successfully.'
        );
    }

    private function ensureTeamLeaderOwnsRegistration(
        Request $request,
        Registration $registration
    ): void {
        $user = $request->user();

        $registration->loadMissing('team');

        abort_unless(
            $user !== null
            &&
            (int) $registration->team->leader_id
                ===
            (int) $user->id,
            403
        );
    }

    private function ensureOrganizerOwnsTournament(
        Request $request,
        Tournament $tournament
    ): void {
        $user = $request->user();

        abort_unless(
            $user !== null
            &&
            $user->hasRole(
                User::ROLE_ORGANIZER
            )
            &&
            (int) $tournament->organizer_id
                ===
            (int) $user->id,
            403
        );
    }

    private function ensurePaymentBelongsToTournament(
        Payment $payment,
        Tournament $tournament
    ): void {
        $payment->loadMissing(
            'registration'
        );

        abort_unless(
            (int) $payment
                ->registration
                ->tournament_id
                ===
            (int) $tournament->id,
            404
        );
    }
}