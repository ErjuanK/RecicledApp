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
        // 1. Tabla: artista
        if (!Schema::hasTable('artista')) {
            Schema::create('artista', function (Blueprint $table) {
                $table->id('artista_id');
                $table->string('nombre_artistico', 255)->unique();
                $table->text('biografia')->nullable();
                $table->string('foto_url', 255)->nullable();
                $table->timestamp('fecha_creacion')->useCurrent();
            });
        }

        // 2. Tabla: genero
        if (!Schema::hasTable('genero')) {
            Schema::create('genero', function (Blueprint $table) {
                $table->id('genero_id');
                $table->string('nombre', 100)->unique();
            });
        }

        // 3. Tabla: artista_genero
        if (!Schema::hasTable('artista_genero')) {
            Schema::create('artista_genero', function (Blueprint $table) {
                $table->unsignedBigInteger('artista_id');
                $table->unsignedBigInteger('genero_id');
                
                $table->primary(['artista_id', 'genero_id']);
                
                $table->foreign('artista_id')->references('artista_id')->on('artista')->onDelete('cascade');
                $table->foreign('genero_id')->references('genero_id')->on('genero')->onDelete('cascade');
            });
        }

        // 4. Tabla: artista_editor (Relación Usuario-Artista)
        if (!Schema::hasTable('artista_editor')) {
            Schema::create('artista_editor', function (Blueprint $table) {
                $table->unsignedBigInteger('usuario_id');
                $table->unsignedBigInteger('artista_id');
                $table->date('fecha_asignacion');

                $table->primary(['usuario_id', 'artista_id']);

                // Asumimos que la tabla de usuarios se llama 'usuario' según el legacy, 
                // pero si es Laravel default 'users', habría que ajustar. 
                // Dado el código visto en ArtistPanelController: User::where('usuario_id'...), la tabla es 'usuario'.
                $table->foreign('usuario_id')->references('usuario_id')->on('usuario')->onDelete('cascade');
                $table->foreign('artista_id')->references('artista_id')->on('artista')->onDelete('cascade');
            });
        }

        // 5. Tabla: album
        if (!Schema::hasTable('album')) {
            Schema::create('album', function (Blueprint $table) {
                $table->id('album_id');
                $table->unsignedBigInteger('artista_id');
                $table->string('titulo', 255);
                $table->date('fecha_lanzamiento')->nullable();
                $table->text('contexto')->nullable();
                $table->string('portada_url', 255)->nullable();
                $table->enum('estado', ['publico', 'privado', 'oculto'])->default('publico');
                $table->timestamp('fecha_creacion')->useCurrent();

                $table->foreign('artista_id')->references('artista_id')->on('artista')->onDelete('cascade');
            });
        }

        // 6. Tabla: cancion
        if (!Schema::hasTable('cancion')) {
            Schema::create('cancion', function (Blueprint $table) {
                $table->id('cancion_id');
                $table->unsignedBigInteger('album_id');
                $table->string('titulo', 255);
                $table->integer('duracion')->nullable(); // En segundos
                $table->text('contexto')->nullable();
                $table->text('creditos')->nullable();
                $table->enum('estado', ['publico', 'privado', 'oculto'])->default('publico');
                $table->timestamp('fecha_creacion')->useCurrent();

                $table->foreign('album_id')->references('album_id')->on('album')->onDelete('cascade');
            });
        }

        // 7. Tabla: letra
        if (!Schema::hasTable('letra')) {
            Schema::create('letra', function (Blueprint $table) {
                $table->id('letra_id');
                $table->unsignedBigInteger('cancion_id')->unique();
                $table->longText('contenido');

                $table->foreign('cancion_id')->references('cancion_id')->on('cancion')->onDelete('cascade');
            });
        }

        // 8. Tabla: anotacion
        if (!Schema::hasTable('anotacion')) {
            Schema::create('anotacion', function (Blueprint $table) {
                $table->id('anotacion_id');
                $table->unsignedBigInteger('letra_id');
                $table->unsignedBigInteger('usuario_id');
                $table->text('explicacion');
                $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
                $table->timestamp('fecha_creacion')->useCurrent();

                $table->foreign('letra_id')->references('letra_id')->on('letra')->onDelete('cascade');
                $table->foreign('usuario_id')->references('usuario_id')->on('usuario')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anotacion');
        Schema::dropIfExists('letra');
        Schema::dropIfExists('cancion');
        Schema::dropIfExists('album');
        Schema::dropIfExists('artista_editor');
        Schema::dropIfExists('artista_genero');
        Schema::dropIfExists('genero');
        Schema::dropIfExists('artista');
    }
};
