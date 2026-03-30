<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Illuminate\Support\Facades\Auth::loginUsingId(1);
$user = Illuminate\Support\Facades\Auth::user();
echo "Logged in: " . $user->email . "\n";

$seenTrackIds = App\Models\UserSongAction::where('user_id', $user->id)
    ->pluck('spotify_track_id')
    ->toArray();
echo "Tracks seen by user: " . count($seenTrackIds) . "\n";

// Test simple Spotify query 
$spotify = app(App\Services\SpotifyService::class);
$rawRecommendations = $spotify->searchTracks('top hits españa', 50);
echo "Spotify returned: " . count($rawRecommendations) . " tracks.\n\n";

// Test iTunes for first available track
$found = 0;
foreach (array_slice($rawRecommendations, 0, 5) as $track) {
    $name = $track['name'] ?? '?';
    $artist = $track['artists'][0]['name'] ?? '?';
    $spotifyPreview = $track['preview_url'] ?? null;
    
    // Try iTunes
    $query = urlencode($artist . ' ' . $name);
    $iTunesUrl = "https://itunes.apple.com/search?term={$query}&entity=song&limit=1";
    $json = @file_get_contents($iTunesUrl, false, stream_context_create(['http'=>['timeout'=>5]]));
    $itunesPreview = null;
    if ($json) {
        $data = json_decode($json, true);
        if (!empty($data['results']) && !empty($data['results'][0]['previewUrl'])) {
            $itunesPreview = $data['results'][0]['previewUrl'];
        }
    }
    
    echo "Track: {$name} ({$artist})\n";
    echo "  Spotify preview: " . ($spotifyPreview ? 'YES' : 'NO') . "\n";
    echo "  iTunes preview:  " . ($itunesPreview ? 'YES - ' . substr($itunesPreview, 0, 60) : 'NO') . "\n\n";
    if ($itunesPreview) $found++;
}
echo "Total with iTunes audio: $found/5\n";
