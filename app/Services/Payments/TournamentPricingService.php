<?php

namespace App\Services\Payments;

use App\Models\Tournament;
use Illuminate\Support\Facades\DB;

class TournamentPricingService
{
    /**
     * Update a tournament's pricing mode (F9).
     * Enforces the free-flow bypass rule: free tournaments always have entry_fee = 0.
     */
    public function updatePricing(Tournament $tournament, array $data): Tournament
    {
        return DB::transaction(function () use ($tournament, $data) {
            $isPaid = (bool) $data['is_paid'];

            $tournament->is_paid   = $isPaid;
            $tournament->entry_fee = $isPaid ? $data['entry_fee'] : 0;
            $tournament->save();

            return $tournament->fresh();
        });
    }

    /**
     * Contract method for other modules (F7 Registration – M1, F10 Payment – M3)
     * to check whether a payment step is required before confirmation.
     */
    public function requiresPayment(Tournament $tournament): bool
    {
        return $tournament->is_paid && $tournament->entry_fee > 0;
    }
}