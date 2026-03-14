<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cancion', function (Blueprint $table) {
            // Añadimos la columna artista_id justo después del cancion_id
            $table->unsignedBigInteger('artista_id')->nullable()->after('cancion_id');
            
            // Creamos la relación de base de datos
            $table->foreign('artista_id')->references('artista_id')->on('artista')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('cancion', function (Blueprint $table) {
            $table->dropForeign(['artista_id']);
            $table->dropColumn('artista_id');
        });
    }
};