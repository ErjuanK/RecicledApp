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
        $this->apiKey = env('LASTFM_API_KEY', '4a9f5581a9cdf20a699f540ac52a95c9');
    }

    /**
     * Get track info including global playcount (scrobbles).
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
                return 0;
            } catch (\Exception $e) {
                Log::error('LastFmService: Failed to get playcount', [
                    'artist' => $artistName,
                    'track'  => $trackName,
                    'error'  => $e->getMessage(),
                ]);
                return 0;
            }
        });
    }

    /**
     * Get the top albums for a given genre/tag from Last.fm.
     * Used for the weekly editorial feed.
     *
     * @param string $tag  e.g. 'pop', 'hip-hop', 'latin', 'indie'
     * @param int    $limit
     * @return array
     */
    public function getTagTopAlbums(string $tag, int $limit = 5): array
    {
        $cacheKey = "lastfm.tag.albums." . md5($tag . $limit);

        return Cache::remember($cacheKey, 3600 * 6, function () use ($tag, $limit) {
            try {
                $response = Http::timeout(10)->get($this->baseUrl, [
                    'method'  => 'tag.getTopAlbums',
                    'tag'     => $tag,
                    'api_key' => $this->apiKey,
                    'limit'   => $limit,
                    'format'  => 'json',
                ]);

                if ($response->successful()) {
                    $albums = $response->json()['albums']['album'] ?? [];
                    return array_map(fn($a) => [
                        'title'  => $a['name'],
                        'artist' => $a['artist']['name'],
                    ], $albums);
                }
            } catch (\Exception $e) {
                Log::error('LastFmService::getTagTopAlbums error: ' . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Get top albums across multiple genres to build a varied editorial feed.
     * Fetches albums from latin, pop, hip-hop, indie and urban tags.
     *
     * @param int $perTag  How many albums to fetch per genre tag
     * @return array       Flat list of ['title', 'artist'] arrays
     */
    public function getWeeklyEditorialPool(int $perTag = 3): array
    {
        $tags = ['latin', 'pop', 'hip-hop', 'indie', 'urban', 'r&b'];
        $pool = [];
        $seen = [];

        foreach ($tags as $tag) {
            $albums = $this->getTagTopAlbums($tag, $perTag);
            foreach ($albums as $album) {
                $key = strtolower($album['artist'] . '|' . $album['title']);
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $pool[] = $album;
                }
            }
        }

        return $pool;
    }

    /**
     * Get a list of similar artists for a given artist name from Last.fm.
     * Used to power the personalized "Para Ti" discovery section.
     *
     * @param string $artistName
     * @param int    $limit  Max number of similar artists to return
     * @return array         List of artist name strings
     */
    public function getSimilarArtists(string $artistName, int $limit = 5): array
    {
        $cacheKey = "lastfm.similar." . md5(strtolower($artistName) . $limit);

        return Cache::remember($cacheKey, 3600 * 24, function () use ($artistName, $limit) {
            try {
                $response = Http::timeout(8)->get($this->baseUrl, [
                    'method'     => 'artist.getSimilar',
                    'artist'     => $artistName,
                    'api_key'    => $this->apiKey,
                    'limit'      => $limit,
                    'autocorrect'=> 1,
                    'format'     => 'json',
                ]);

                if ($response->successful()) {
                    $artists = $response->json()['similarartists']['artist'] ?? [];
                    return array_column($artists, 'name');
                }
            } catch (\Exception $e) {
                Log::error('LastFmService::getSimilarArtists error: ' . $e->getMessage());
            }
            return [];
        });
    }
}
