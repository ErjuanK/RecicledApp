<?php
$query = urlencode("Bad Bunny Dakiti");
$url = "https://itunes.apple.com/search?term={$query}&entity=song&limit=1";
$json = file_get_contents($url);
$data = json_decode($json, true);
echo "Results: " . $data['resultCount'] . "\n";
if($data['resultCount'] > 0) {
    echo "Preview: " . $data['results'][0]['previewUrl'] . "\n";
}
