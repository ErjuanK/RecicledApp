<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$migrationName = '2026_02_09_134000_add_portada_to_cancion_table';

$exists = DB::table('migrations')->where('migration', $migrationName)->exists();

if (!$exists) {
    echo "Marking migration as run..." . PHP_EOL;
    $batch = DB::table('migrations')->max('batch') + 1;
    DB::table('migrations')->insert([
        'migration' => $migrationName,
        'batch' => $batch
    ]);
    echo "Migration marked as run." . PHP_EOL;
} else {
    echo "Migration already marked as run." . PHP_EOL;
}
