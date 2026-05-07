<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$spotify = new App\Services\SpotifyService();
$artist = $spotify->searchArtist('54R6Y0I7jGUCveDTtI21nb');
print_r($artist);
