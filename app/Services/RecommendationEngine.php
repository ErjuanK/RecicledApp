<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecommendationEngine
{
    private string $apiUrl;
    private int $timeout;

    public function __construct()
    {
        $this->apiUrl = env('RECOMMENDATION_API_URL', 'http://localhost:8000');
        $this->timeout = 10;
    }

    /**
     * Fetch personalized recommendations from the AI microservice.
     *
     * @param array $genres     User's preferred genres
     * @param array $albumIds   Spotify album IDs
     * @param array $trackIds   Spotify track IDs
     * @return array
     */
    public function getRecommendations(array $genres = [], array $albumIds = [], array $trackIds = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->apiUrl}/api/v1/generate-discovery", [
                    'genres'    => $genres,
                    'album_ids' => $albumIds,
                    'track_ids' => $trackIds,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['discovery_dashboard'] ?? $this->getFallbackRecommendations();
            }

            Log::warning('RecommendationEngine: API returned non-success status', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return $this->getFallbackRecommendations();

        } catch (\Exception $e) {
            Log::error('RecommendationEngine: Failed to connect to AI microservice', [
                'error' => $e->getMessage(),
            ]);

            return $this->getFallbackRecommendations();
        }
    }

    /**
     * Return fallback recommendations when the AI service is unavailable.
     */
    private function getFallbackRecommendations(): array
    {
        $fallbackArtists = ['Luna Nova', 'The Midnight Echo', 'Velvet Storm', 'Neon Pulse', 'Crystal Waves'];
        $fallbackTracks = ['Midnight Drive', 'Neon Lights', 'Lost in Time', 'Electric Dreams', 'Skyline',
                           'Afterglow', 'Gravity', 'Echoes', 'Starfall', 'Waves'];

        $playlist = [];
        for ($i = 0; $i < 15; $i++) {
            $playlist[] = [
                'id'     => 'fallback_track_' . $i,
                'name'   => $fallbackTracks[$i % count($fallbackTracks)],
                'artist' => $fallbackArtists[array_rand($fallbackArtists)],
                'url'    => '#',
            ];
        }

        $albums = [];
        $albumTitles = ['Infinite Loop', 'Digital Sunrise'];
        for ($i = 0; $i < 2; $i++) {
            $albums[] = [
                'id'     => 'fallback_album_' . $i,
                'name'   => $albumTitles[$i],
                'artist' => $fallbackArtists[array_rand($fallbackArtists)],
                'image'  => "https://picsum.photos/seed/fallbackAlbum{$i}/400/400",
                'url'    => '#',
            ];
        }

        $singles = [];
        for ($i = 0; $i < 2; $i++) {
            $singles[] = [
                'id'     => 'fallback_single_' . $i,
                'name'   => $fallbackTracks[array_rand($fallbackTracks)],
                'artist' => $fallbackArtists[array_rand($fallbackArtists)],
                'image'  => "https://picsum.photos/seed/fallbackSingle{$i}/300/300",
                'url'    => '#',
            ];
        }

        return [
            'recommended_albums'  => $albums,
            'recommended_singles' => $singles,
            'weekly_playlist'     => $playlist,
        ];
    }
}
