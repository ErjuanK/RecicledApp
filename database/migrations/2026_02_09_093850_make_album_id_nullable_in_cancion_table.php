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
        Schema::table('cancion', function (Blueprint $table) {
            $table->dropForeign(['album_id']);
            $table->unsignedBigInteger('album_id')->nullable()->change();
            $table->foreign('album_id')->references('album_id')->on('album')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cancion', function (Blueprint $table) {
            $table->dropForeign(['album_id']);
            $table->unsignedBigInteger('album_id')->nullable(false)->change();
            $table->foreign('album_id')->references('album_id')->on('album')->onDelete('cascade');
        });
    }
};
