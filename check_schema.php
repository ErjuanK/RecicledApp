<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Table 'cancion' exists: " . (Schema::hasTable('cancion') ? 'YES' : 'NO') . PHP_EOL;
if (Schema::hasTable('cancion')) {
    echo "Columns in 'cancion':" . PHP_EOL;
    $columns = Schema::getColumnListing('cancion');
    print_r($columns);
    
    echo "Column 'portada' exists: " . (Schema::hasColumn('cancion', 'portada') ? 'YES' : 'NO') . PHP_EOL;
    echo "Column 'estado' exists: " . (Schema::hasColumn('cancion', 'estado') ? 'YES' : 'NO') . PHP_EOL;
}
