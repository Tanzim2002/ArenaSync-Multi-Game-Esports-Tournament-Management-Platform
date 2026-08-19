<?php

namespace App\Http\Controllers;

use App\Models\OrganizerVerification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizerVerificationController extends Controller
{
    /**
     * Display the authenticated organizer's verification status.
     */
    public function show(Request $request): View
    {
        $verification = OrganizerVerification::query()
            ->where(
                'organizer_id',
                $request->user()->id
            )
            ->first();

        return view(
            'verification.show',
            compact('verification')
        );
    }

    /**
     * Submit or resubmit an organizer verification request.
     */
    public function store(Request $request): RedirectResponse
    {
        $organizer = $request->user();

        abort_unless(
            $organizer !== null
            && $organizer->hasRole(User::ROLE_ORGANIZER),
            403
        );

        $verification = OrganizerVerification::query()
            ->where(
                'organizer_id',
                $organizer->id
            )
            ->first();

        if ($verification === null) {
            OrganizerVerification::query()->create([
                'organizer_id' => $organizer->id,
                'status' => OrganizerVerification::STATUS_PENDING,
                'requested_at' => now(),
            ]);

            return back()->with(
                'success',
                'Organizer verification request submitted successfully.'
            );
        }

        if ($verification->isPending()) {
            return back()->withErrors([
                'verification' =>
                    'Your organizer verification request is already pending.',
            ]);
        }

        if ($verification->isVerified()) {
            return back()->withErrors([
                'verification' =>
                    'Your organizer account is already verified.',
            ]);
        }

        $verification->update([
            'status' => OrganizerVerification::STATUS_PENDING,
            'requested_at' => now(),
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        return back()->with(
            'success',
            'Organizer verification request resubmitted successfully.'
        );
    }

    /**
     * Display organizer verification requests for administrators.
     */
    public function index(): View
    {
        $verifications = OrganizerVerification::query()
            ->with([
                'organizer',
                'reviewer',
            ])
            ->orderByRaw(
                'CASE WHEN status = ? THEN 0 ELSE 1 END',
                [
                    OrganizerVerification::STATUS_PENDING,
                ]
            )
            ->latest('requested_at')
            ->paginate(15);

        return view(
            'verification.index',
            compact('verifications')
        );
    }

    /**
     * Approve a pending organizer verification request.
     */
    public function approve(
        Request $request,
        OrganizerVerification $verification
    ): RedirectResponse {
        if (! $verification->isPending()) {
            return back()->withErrors([
                'verification' =>
                    'Only pending organizer verification requests can be approved.',
            ]);
        }

        $verification->update([
            'status' => OrganizerVerification::STATUS_VERIFIED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with(
            'success',
            'Organizer verification approved successfully.'
        );
    }

    /**
     * Reject a pending organizer verification request.
     */
    public function reject(
        Request $request,
        OrganizerVerification $verification
    ): RedirectResponse {
        if (! $verification->isPending()) {
            return back()->withErrors([
                'verification' =>
                    'Only pending organizer verification requests can be rejected.',
            ]);
        }

        $verification->update([
            'status' => OrganizerVerification::STATUS_REJECTED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with(
            'success',
            'Organizer verification rejected successfully.'
        );
    }
}
