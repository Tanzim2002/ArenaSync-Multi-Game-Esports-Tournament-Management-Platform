<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table): void {
            $table->boolean('is_paid')
                ->default(false)
                ->after('prize_type');

            $table->decimal('entry_fee', 10, 2)
                ->default(0)
                ->after('is_paid');
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table): void {
            $table->dropColumn(['is_paid', 'entry_fee']);
        });
    }
};