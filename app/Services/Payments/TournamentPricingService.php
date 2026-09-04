<?php

namespace App\Services\Payments;

use App\Models\Tournament;
use Illuminate\Support\Facades\DB;

class TournamentPricingService
{
    public function updatePricing(
        Tournament $tournament,
        array $data
    ): Tournament {
        return DB::transaction(
            function () use (
                $tournament,
                $data
            ): Tournament {
                $isPaid =
                    (bool) $data['is_paid'];

                $tournament->is_paid =
                    $isPaid;

                $tournament->entry_fee =
                    $isPaid
                        ? $data['entry_fee']
                        : 0;

                $tournament->save();

                return $tournament->fresh();
            }
        );
    }

    public function requiresPayment(
        Tournament $tournament
    ): bool {
        return $tournament
            ->requiresPayment();
    }
}