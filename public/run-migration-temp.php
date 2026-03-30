<?php
// Script temporal para ejecutar la migracion de user_likes
// Accede via: http://127.0.0.1:8000/run-migration-temp (solo una vez)

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasTable('user_likes')) {
    Schema::create('user_likes', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->enum('type', ['song', 'album', 'artist']);
        $table->string('spotify_id');
        $table->string('name');
        $table->string('artist_name')->nullable();
        $table->string('image_url')->nullable();
        $table->string('external_url')->nullable();
        $table->json('extra')->nullable();
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->unique(['user_id', 'type', 'spotify_id']);
    });

    // Registrar en migrations table
    DB::table('migrations')->insert([
        'migration' => '2026_03_30_200000_create_user_likes_table',
        'batch'     => DB::table('migrations')->max('batch') + 1,
    ]);

    echo "✅ Tabla user_likes creada con exito. Puedes borrar este archivo.";
} else {
    echo "ℹ️ La tabla user_likes ya existia. No se hizo nada.";
}
