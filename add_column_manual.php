<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    if (!Schema::hasColumn('cancion', 'portada')) {
        echo "Adding 'portada' column..." . PHP_EOL;
        DB::statement("ALTER TABLE cancion ADD COLUMN portada VARCHAR(255) NULL AFTER estado");
        echo "Column added successfully." . PHP_EOL;
    } else {
        echo "Column 'portada' already exists." . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
