<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('team_membership_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('team_id')
                ->constrained('teams')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('requested_by_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('request_type', [
                'INVITATION',
                'JOIN_REQUEST',
            ]);

            $table->enum('status', [
                'PENDING',
                'ACCEPTED',
                'REJECTED',
                'CANCELLED',
            ])->default('PENDING');

            $table->foreignId('responded_by_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('responded_at')->nullable();

            $table->timestamps();

            $table->index([
                'team_id',
                'user_id',
                'status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_membership_requests');
    }
};