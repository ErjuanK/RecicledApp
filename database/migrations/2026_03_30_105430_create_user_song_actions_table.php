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
        Schema::create('user_song_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('spotify_track_id');
            $table->string('album_id')->nullable(); 
            $table->enum('action', ['like', 'dislike']);
            $table->timestamps();

            // Prevent duplicate actions per song per user
            $table->unique(['user_id', 'spotify_track_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_song_actions');
    }
};
