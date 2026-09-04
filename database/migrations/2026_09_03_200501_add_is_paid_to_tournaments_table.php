<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table): void {
            if (! Schema::hasColumn('tournaments', 'is_paid')) {
                $table->boolean('is_paid')
                    ->default(false);
            }

            if (! Schema::hasColumn('tournaments', 'entry_fee')) {
                $table->decimal('entry_fee', 12, 2)
                    ->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table): void {
            if (Schema::hasColumn('tournaments', 'entry_fee')) {
                $table->dropColumn('entry_fee');
            }

            if (Schema::hasColumn('tournaments', 'is_paid')) {
                $table->dropColumn('is_paid');
            }
        });
    }
};