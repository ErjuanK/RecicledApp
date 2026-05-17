<?php

namespace App\Http\Controllers;

use App\Services\ItunesService;
use Illuminate\Http\Request;

class DiscoveryController extends Controller
{
    private ItunesService $itunes;

    public function __construct(ItunesService $itunes)
    {
        $this->itunes = $itunes;
    }

    /**
     * Show the onboarding wizard (genre → album → track selection).
     */
    public function showOnboarding()
    {
        return view('discovery.onboarding');
    }

    /**
     * Return the list of Spotify seed genres.
     */
    public function getAvailableGenres()
    {
        // Curated list of Spotify seed genres (subset of available_genre_seeds)
        $genres = [
            'acoustic', 'afrobeat', 'alt-rock', 'alternative', 'ambient',
            'blues', 'bossanova', 'brazil', 'breakbeat', 'british',
            'chill', 'classical', 'club', 'country', 'dance',
            'dancehall', 'deep-house', 'disco', 'drum-and-bass', 'dub',
            'dubstep', 'edm', 'electro', 'electronic', 'emo',
            'folk', 'funk', 'garage', 'german', 'gospel',
            'goth', 'grindcore', 'groove', 'grunge', 'guitar',
            'happy', 'hard-rock', 'hardcore', 'hardstyle', 'heavy-metal',
            'hip-hop', 'house', 'idm', 'indie', 'indie-pop',
            'industrial', 'j-pop', 'j-rock', 'jazz', 'k-pop',
            'latin', 'latino', 'malay', 'metal', 'metalcore',
            'minimal-techno', 'new-age', 'opera', 'party', 'piano',
            'pop', 'pop-film', 'post-dubstep', 'power-pop', 'progressive-house',
            'psych-rock', 'punk', 'punk-rock', 'r-n-b', 'rainy-day',
            'rap', 'reggae', 'reggaeton', 'rock', 'rock-n-roll', 'rockabilly',
            'romance', 'sad', 'salsa', 'samba', 'show-tunes',
            'singer-songwriter', 'ska', 'sleep', 'soul', 'spanish',
            'study', 'summer', 'synth-pop', 'tango', 'techno',
            'trance', 'trap', 'trip-hop', 'turkish', 'world-music',
        ];

        return response()->json($genres);
    }

    /**
     * Search iTunes for albums or tracks.
     * GET /discovery/search?q=query&type=album|track
     */
    public function searchSpotify(Request $request)
    {
        $query = $request->input('q', '');
        $type = $request->input('type', 'album');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        if ($type === 'album') {
            $albums = $this->itunes->searchAlbums($query, 8);
            foreach ($albums as $album) {
                $results[] = [
                    'id'     => $album['id'],
                    'name'   => $album['name'],
                    'artist' => $album['artists'][0]['name'] ?? 'Unknown',
                    'image'  => $album['images'][0]['url'] ?? null,
                    'year'   => substr($album['release_date'] ?? '', 0, 4),
                    'url'    => $album['external_urls']['spotify'] ?? '#',
                ];
            }
        } elseif ($type === 'track') {
            $tracks = $this->itunes->searchTracks($query, 8);
            foreach ($tracks as $track) {
                $minutes = floor(($track['duration_ms'] ?? 0) / 60000);
                $seconds = round((($track['duration_ms'] ?? 0) % 60000) / 1000);
                $results[] = [
                    'id'       => $track['id'],
                    'name'     => $track['name'],
                    'artist'   => $track['artists'][0]['name'] ?? 'Unknown',
                    'image'    => $track['album']['images'][0]['url'] ?? null,
                    'album'    => $track['album']['name'] ?? '',
                    'duration' => sprintf("%d:%02d", $minutes, $seconds),
                    'url'      => $track['external_urls']['spotify'] ?? '#',
                ];
            }
        }

        return response()->json($results);
    }

    /**
     * Receive user selections and generate the discovery dashboard.
     * Uses Spotify's recommendations API directly for real results.
     * POST /discovery/generate
     */
    public function generateDashboard(Request $request)
    {
        $genres   = $request->input('genres', []);
        $albumIds = $request->input('album_ids', []);
        $trackIds = $request->input('track_ids', []);

        try {
            $recommendations = $this->itunes->getRecommendations($genres, $albumIds, $trackIds);
            return response()->json($recommendations);
        } catch (\Exception $e) {
            \Log::error('Discovery recommendations failed: ' . $e->getMessage());
            return response()->json([
                'recommended_albums'  => [],
                'recommended_singles' => [],
                'weekly_playlist'     => [],
            ]);
        }
    }
}
