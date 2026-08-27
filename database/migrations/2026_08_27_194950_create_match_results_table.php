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
    Schema::create('match_results', function (Blueprint $table) {
    $table->id();

    $table->foreignId('match_id')
    ->unique()
    ->constrained('matches')
    ->cascadeOnDelete();

    $table->unsignedInteger('team_one_score');
    $table->unsignedInteger('team_two_score');

    $table->foreignId('winner_team_id')
        ->nullable()
        ->constrained('teams')
        ->nullOnDelete();

    $table->string('status', 20)
        ->default('PENDING')
        ->index();

    $table->text('remarks')->nullable();

    $table->foreignId('submitted_by')
        ->constrained('users');

    $table->timestamp('submitted_at')
        ->useCurrent();

    $table->foreignId('reviewed_by')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

    $table->timestamp('reviewed_at')->nullable();
    $table->text('review_notes')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_results');
    }
};
