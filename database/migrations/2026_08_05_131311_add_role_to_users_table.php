<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the platform role to every ArenaSync user.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 20)
                ->default('PLAYER')
                ->index();
        });
    }

    /**
     * Remove the platform role if the migration is rolled back.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('role');
        });
    }
};