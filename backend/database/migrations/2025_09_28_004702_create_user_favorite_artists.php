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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('artist_id');
            $table->timestamps();
            $table->unsignedBigInteger('last_checked_album')->nullable();
            $table->boolean('listening_now')->default(false);

            $table->primary(['user_id', 'artist_id']);
            $table->index(['listening_now']);
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
