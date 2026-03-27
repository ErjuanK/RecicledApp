<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LastFmService
{
    private string $apiKey;
    private string $baseUrl = 'http://ws.audioscrobbler.com/2.0/';

    public function __construct()
    {
        // Proveemos una clave pública por defecto en caso de no estar en el .env
        $this->apiKey = env('LASTFM_API_KEY', '4a9f5581a9cdf20a699f540ac52a95c9');
    }

    /**
     * Get track info including global playcount (scrobbles).
     *
     * @param string $artistName
     * @param string $trackName
     * @return int
     */
    public function getTrackPlaycount(string $artistName, string $trackName): int
    {
        $cacheKey = "lastfm.playcount." . md5(strtolower($artistName . $trackName));

        return Cache::remember($cacheKey, 86400, function () use ($artistName, $trackName) {
            try {
                $response = Http::timeout(5)->get($this->baseUrl, [
                    'method'  => 'track.getInfo',
                    'api_key' => $this->apiKey,
                    'artist'  => $artistName,
                    'track'   => $trackName,
                    'format'  => 'json'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['track']['playcount'])) {
                        return (int) $data['track']['playcount'];
                    }
                }
                
                return 0; // Fallback si la canción no existe en Last.fm
            } catch (\Exception $e) {
                Log::error('LastFmService: Failed to get playcount', [
                    'artist' => $artistName,
                    'track'  => $trackName,
                    'error'  => $e->getMessage(),
                ]);
                return 0; // Fallback en caso de timeout
            }
        });
    }
}
