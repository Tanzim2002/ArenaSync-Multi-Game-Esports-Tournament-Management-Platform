<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the games table used by tournaments and teams.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table): void {
            $table->id();

            $table->string('name', 150)
                ->unique();

            $table->string('genre', 100);

            $table->string('platform', 100);

            $table->text('rules');

            $table->unsignedSmallInteger('team_size');

            $table->timestamps();
        });
    }

    /**
     * Remove the games table.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};