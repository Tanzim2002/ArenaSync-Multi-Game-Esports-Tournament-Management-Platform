<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the tournaments table.
     */
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('organizer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('game_id')
                ->constrained();

            $table->string('title', 180);

            $table->text('description');

            $table->dateTime('registration_deadline');

            $table->dateTime('start_at');

            $table->dateTime('end_at');

            $table->text('rules');

            $table->decimal('prize_pool', 12, 2)
                ->default(0);

            $table->unsignedInteger('team_limit');

            $table->string('match_format', 100);

            $table->string('status', 30)
                ->default('DRAFT')
                ->index();

            $table->timestamps();
        });
    }

    /**
     * Remove the tournaments table.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};