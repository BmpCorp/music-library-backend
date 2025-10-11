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
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('title', 512);
            $table->text('description')->nullable();
            $table->text('genres')->nullable();

            $table->foreignId('country_id')
                ->nullable()
                ->references('id')
                ->on('countries')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->index(['title', 'deleted_at']);
            $table->index(['country_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artists');
    }
};
