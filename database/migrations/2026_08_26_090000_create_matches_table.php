<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('tournament_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('round', 100);

            $table->foreignId('team_one_id')
                ->constrained('teams')
                ->cascadeOnDelete();

            $table->foreignId('team_two_id')
                ->nullable()
                ->constrained('teams')
                ->nullOnDelete();

            $table->dateTime('scheduled_at');

            $table->string('status', 20)
                ->default('SCHEDULED')
                ->index();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['tournament_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};