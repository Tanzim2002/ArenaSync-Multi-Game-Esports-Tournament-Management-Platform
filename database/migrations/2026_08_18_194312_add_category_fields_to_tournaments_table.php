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
        Schema::table('tournaments', function (Blueprint $table): void {
            $table->string('category', 100)
                ->nullable()
                ->after('game_id');

            $table->string('region', 100)
                ->nullable()
                ->after('category');

            $table->string('prize_type', 50)
                ->nullable()
                ->after('region');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table): void {
            $table->dropColumn([
                'category',
                'region',
                'prize_type',
            ]);
        });
    }
};