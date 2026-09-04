<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | F9 - Tournament Pricing
        |--------------------------------------------------------------------------
        */

        if (Schema::hasTable('tournaments')) {
            $hasIsPaid = Schema::hasColumn(
                'tournaments',
                'is_paid'
            );

            $hasEntryFee = Schema::hasColumn(
                'tournaments',
                'entry_fee'
            );

            Schema::table(
                'tournaments',
                function (Blueprint $table) use (
                    $hasIsPaid,
                    $hasEntryFee
                ): void {
                    if (! $hasIsPaid) {
                        $table
                            ->boolean('is_paid')
                            ->default(false);
                    }

                    if (! $hasEntryFee) {
                        $table
                            ->decimal(
                                'entry_fee',
                                12,
                                2
                            )
                            ->default(0);
                    }
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | F10 / F11 - Payment Submission & Verification
        |--------------------------------------------------------------------------
        */

        if (! Schema::hasTable('payments')) {
            return;
        }

        $hasMethod = Schema::hasColumn(
            'payments',
            'method'
        );

        $hasReference = Schema::hasColumn(
            'payments',
            'reference'
        );

        $hasSubmittedAt = Schema::hasColumn(
            'payments',
            'submitted_at'
        );

        $hasReviewedBy = Schema::hasColumn(
            'payments',
            'reviewed_by'
        );

        $hasReviewedAt = Schema::hasColumn(
            'payments',
            'reviewed_at'
        );

        Schema::table(
            'payments',
            function (Blueprint $table) use (
                $hasMethod,
                $hasReference,
                $hasSubmittedAt,
                $hasReviewedBy,
                $hasReviewedAt
            ): void {
                if (! $hasMethod) {
                    $table
                        ->string(
                            'method',
                            100
                        )
                        ->nullable();
                }

                if (! $hasReference) {
                    $table
                        ->string(
                            'reference',
                            150
                        )
                        ->nullable();
                }

                if (! $hasSubmittedAt) {
                    $table
                        ->timestamp(
                            'submitted_at'
                        )
                        ->nullable();
                }

                if (! $hasReviewedBy) {
                    $table
                        ->unsignedBigInteger(
                            'reviewed_by'
                        )
                        ->nullable();
                }

                if (! $hasReviewedAt) {
                    $table
                        ->timestamp(
                            'reviewed_at'
                        )
                        ->nullable();
                }
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Preserve any payments created by the temporary recovery schema.
        |--------------------------------------------------------------------------
        */

        $payments = DB::table('payments')
            ->orderBy('id')
            ->get();

        foreach ($payments as $payment) {
            $transactionId = property_exists(
                $payment,
                'transaction_id'
            )
                ? $payment->transaction_id
                : null;

            $verifiedBy = property_exists(
                $payment,
                'verified_by'
            )
                ? $payment->verified_by
                : null;

            $verifiedAt = property_exists(
                $payment,
                'verified_at'
            )
                ? $payment->verified_at
                : null;

            DB::table('payments')
                ->where('id', $payment->id)
                ->update([
                    'method' => $payment->method
                        ?? 'Other',

                    'reference' => $payment->reference
                        ?? $transactionId
                        ?? 'LEGACY-'.$payment->id,

                    'submitted_at' => $payment->submitted_at
                        ?? $payment->created_at
                        ?? now(),

                    'reviewed_by' => $payment->reviewed_by
                        ?? $verifiedBy,

                    'reviewed_at' => $payment->reviewed_at
                        ?? $verifiedAt,
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            $columns = [];

            foreach (
                [
                    'method',
                    'reference',
                    'submitted_at',
                    'reviewed_by',
                    'reviewed_at',
                ] as $column
            ) {
                if (
                    Schema::hasColumn(
                        'payments',
                        $column
                    )
                ) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                Schema::table(
                    'payments',
                    function (
                        Blueprint $table
                    ) use ($columns): void {
                        $table->dropColumn(
                            $columns
                        );
                    }
                );
            }
        }

        if (
            Schema::hasTable('tournaments')
            && Schema::hasColumn(
                'tournaments',
                'entry_fee'
            )
        ) {
            Schema::table(
                'tournaments',
                function (
                    Blueprint $table
                ): void {
                    $table->dropColumn(
                        'entry_fee'
                    );
                }
            );
        }
    }
};