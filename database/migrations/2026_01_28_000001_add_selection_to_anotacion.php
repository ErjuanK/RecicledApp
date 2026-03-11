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
        Schema::table('anotacion', function (Blueprint $table) {
            if (!Schema::hasColumn('anotacion', 'texto_seleccionado')) {
                $table->text('texto_seleccionado')->nullable()->after('letra_id');
            }
            if (!Schema::hasColumn('anotacion', 'start_offset')) {
                $table->integer('start_offset')->nullable()->after('texto_seleccionado');
            }
            if (!Schema::hasColumn('anotacion', 'end_offset')) {
                $table->integer('end_offset')->nullable()->after('start_offset');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anotacion', function (Blueprint $table) {
            $table->dropColumn(['texto_seleccionado', 'start_offset', 'end_offset']);
        });
    }
};
