<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\UpdateTournamentPricingRequest;
use App\Models\Tournament;
use App\Policies\TournamentPricingPolicy;
use App\Services\Payments\TournamentPricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TournamentPricingController extends Controller
{
    public function __construct(
        protected TournamentPricingService $pricingService,
        protected TournamentPricingPolicy $pricingPolicy
    ) {
    }

    public function edit(
        Tournament $tournament
    ): View {
        $this->authorizeAccess(
            $tournament
        );

        return view(
            'payments.pricing.edit',
            compact('tournament')
        );
    }

    public function update(
        UpdateTournamentPricingRequest $request,
        Tournament $tournament
    ): RedirectResponse {
        $this->authorizeAccess(
            $tournament
        );

        $this->pricingService
            ->updatePricing(
                $tournament,
                $request->validated()
            );

        return redirect()
            ->route(
                'tournaments.pricing.edit',
                $tournament
            )
            ->with(
                'success',
                'Tournament pricing updated successfully.'
            );
    }

    public function updateApi(
        UpdateTournamentPricingRequest $request,
        Tournament $tournament
    ): JsonResponse {
        $this->authorizeAccess(
            $tournament
        );

        $tournament =
            $this->pricingService
                ->updatePricing(
                    $tournament,
                    $request->validated()
                );

        return response()->json([
            'id'
                => $tournament->id,

            'is_paid'
                => $tournament->is_paid,

            'entry_fee'
                => $tournament->entry_fee,

            'status'
                => $tournament->status,
        ]);
    }

    private function authorizeAccess(
        Tournament $tournament
    ): void {
        abort_unless(
            $this->pricingPolicy->manage(
                auth()->user(),
                $tournament
            ),
            403
        );
    }
}