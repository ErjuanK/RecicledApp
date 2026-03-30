<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$spotify = app(App\Services\SpotifyService::class);
$tracks = $spotify->searchTracks('bad bunny', 50);
$previews = collect($tracks)->pluck('preview_url')->filter()->values();
echo "Bad Bunny - Total: " . count($tracks) . "\n";
echo "Bad Bunny - With Audio: " . count($previews) . "\n";

$tracks2 = $spotify->searchTracks('pop', 50);
$previews2 = collect($tracks2)->pluck('preview_url')->filter()->values();
echo "Pop - Total: " . count($tracks2) . "\n";
echo "Pop - With Audio: " . count($previews2) . "\n";
