<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('registration_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('amount', 12, 2);

            $table->string('method', 100);

            $table->string('reference', 150)
                ->unique();

            $table->string('status', 20)
                ->default('PENDING')
                ->index();

            $table->timestamp('submitted_at')
                ->useCurrent();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};