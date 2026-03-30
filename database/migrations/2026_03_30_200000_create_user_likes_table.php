<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['song', 'album', 'artist']);
            $table->string('spotify_id');          // ID único de Spotify/iTunes
            $table->string('name');                // Nombre de la canción / álbum / artista
            $table->string('artist_name')->nullable();  // Para canciones y álbumes
            $table->string('image_url')->nullable();    // Portada
            $table->string('external_url')->nullable(); // Enlace a Spotify/Apple Music
            $table->json('extra')->nullable();          // Datos adicionales (duración, preview_url…)
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Un usuario no puede dar likex2 al mismo elemento del mismo tipo
            $table->unique(['user_id', 'type', 'spotify_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_likes');
    }
};
