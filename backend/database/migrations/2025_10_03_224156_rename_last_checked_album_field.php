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
        Schema::table('user_favorite_artists', function (Blueprint $table) {
            $table->renameColumn('last_checked_album', 'last_checked_album_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_favorite_artists', function (Blueprint $table) {
            $table->renameColumn('last_checked_album_id', 'last_checked_album');
        });
    }
};
