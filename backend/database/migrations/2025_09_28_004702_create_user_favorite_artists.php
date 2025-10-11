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
        Schema::create('user_favorite_artists', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('artist_id')
                ->references('id')
                ->on('artists')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('last_checked_album')
                ->nullable()
                ->references('id')
                ->on('albums')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->boolean('listening_now')->default(false);

            $table->index(['user_id', 'artist_id']);
            $table->index(['user_id', 'listening_now']);
            $table->index(['artist_id', 'listening_now']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_artist_favorites');
    }
};
