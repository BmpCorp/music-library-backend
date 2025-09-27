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
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('title', 512);
            $table->text('description');
            $table->text('genres');
            $table->unsignedBigInteger('artist_id')->nullable(false);
            $table->unsignedSmallInteger('year');
            $table->unsignedBigInteger('song_count');
            $table->boolean('has_explicit_lyrics')->default(false);

            $table->index(['title', 'deleted_at']);
            $table->index(['artist_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
