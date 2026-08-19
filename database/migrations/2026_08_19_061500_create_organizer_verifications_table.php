<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the organizer verification request table.
     */
    public function up(): void
    {
        Schema::create('organizer_verifications', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('organizer_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('status', 20)
                ->default('PENDING')
                ->index();

            $table->timestamp('requested_at');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Remove the organizer verification request table.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizer_verifications');
    }
};
