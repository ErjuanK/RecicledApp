<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nombre_real', 100)->nullable();
            $table->string('apellidos', 100)->nullable();
            $table->string('calle', 255)->nullable();
            $table->string('codigo_postal', 20)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('pais', 100)->nullable();
            $table->string('avatar')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nombre_real', 'apellidos', 'calle', 'codigo_postal', 'ciudad', 'pais', 'avatar'
            ]);
        });
    }
};