<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Compatibility
        |--------------------------------------------------------------------------
        |
        | Older ArenaSync branches created this table from a placeholder-named
        | migration. If that table already exists, leave it untouched and allow
        | Laravel to record this canonical migration as completed.
        |
        */

        if (Schema::hasTable('livestreams')) {
            return;
        }

        Schema::create(
            'livestreams',
            function (Blueprint $table): void {
                $table->id();

                $table->foreignId('tournament_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('added_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->enum(
                    'platform',
                    [
                        'youtube',
                        'twitch',
                        'facebook',
                    ]
                );

                $table->string('url');

                $table->string('label')
                    ->nullable();

                $table->boolean('is_active')
                    ->default(true);

                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('livestreams');
    }
};