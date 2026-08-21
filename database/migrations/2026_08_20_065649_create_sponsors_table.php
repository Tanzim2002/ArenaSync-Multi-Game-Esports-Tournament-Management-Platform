<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsors', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tournament_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('logo')
                ->nullable();

            $table->string('website')
                ->nullable();

            $table->string('contact_person')
                ->nullable();

            $table->string('email')
                ->nullable();

            $table->string('phone')
                ->nullable();

            $table->string('sponsorship_type')
                ->nullable();

            $table->decimal('amount', 10, 2)
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};

