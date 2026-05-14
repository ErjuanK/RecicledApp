<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ItunesService
{
    /**
     * Search for albums by artist/keyword.
     * Maps the iTunes response to match the Spotify array structure.
     */
    public function searchAlbums(string $query, int $limit = 10): array
    {
        $cacheKey = "itunes.search.albums." . md5($query . $limit);

        return Cache::remember($cacheKey, 86400, function () use ($query, $limit) {
            try {
                $response = Http::get("https://itunes.apple.com/search", [
                    'term'   => $query,
                    'entity' => 'album',
                    'limit'  => $limit
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $mappedResults = [];

                    foreach ($data['results'] ?? [] as $result) {
                        $highResImage = str_replace('100x100bb.jpg', '600x600bb.jpg', $result['artworkUrl100'] ?? '');
                        $mappedResults[] = [
                            'id'            => $result['collectionId'],
                            'name'          => $result['collectionName'],
                            'artists'       => [['name' => $result['artistName']]],
                            'images'        => [['url' => $highResImage]],
                            'release_date'  => $result['releaseDate'] ?? now()->toIso8601String(),
                            'album_type'    => 'ALBUM',
                            'total_tracks'  => $result['trackCount'] ?? 1,
                            'external_urls' => ['spotify' => $result['collectionViewUrl'] ?? '#']
                        ];
                    }
                    return $mappedResults;
                }
            } catch (\Exception $e) {
                Log::error('ItunesService::searchAlbums Error: ' . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Get full album details (cover + tracklist) from iTunes by searching artist + album name.
     * Returns a structured object compatible with the album view.
     */
    public function getAlbumBySearch(string $artist, string $albumName): ?object
    {
        $cacheKey = "itunes.album.detail." . md5($artist . $albumName);

        return Cache::remember($cacheKey, 86400, function () use ($artist, $albumName) {
            try {
                // Step 1: Find the album collection ID
                $searchResponse = Http::get("https://itunes.apple.com/search", [
                    'term'    => $artist . ' ' . $albumName,
                    'entity'  => 'album',
                    'limit'   => 5,
                ]);

                if (!$searchResponse->successful()) return null;

                $results = $searchResponse->json()['results'] ?? [];
                $collection = null;

                // Try to find the best match
                foreach ($results as $r) {
                    if (stripos($r['collectionName'], $albumName) !== false &&
                        stripos($r['artistName'], $artist) !== false) {
                        $collection = $r;
                        break;
                    }
                }
                // Fallback: use first result
                if (!$collection && !empty($results)) {
                    $collection = $results[0];
                }

                if (!$collection) return null;

                $collectionId = $collection['collectionId'];
                $highResImage = str_replace('100x100bb.jpg', '600x600bb.jpg', $collection['artworkUrl100'] ?? '');

                // Step 2: Get the full tracklist
                $lookupResponse = Http::get("https://itunes.apple.com/lookup", [
                    'id'     => $collectionId,
                    'entity' => 'song',
                ]);

                $tracks = [];
                if ($lookupResponse->successful()) {
                    $lookupData = $lookupResponse->json()['results'] ?? [];
                    foreach ($lookupData as $item) {
                        if (($item['wrapperType'] ?? '') === 'track') {
                            $durationMs = $item['trackTimeMillis'] ?? 0;
                            $minutes = floor($durationMs / 60000);
                            $seconds = round(($durationMs % 60000) / 1000);
                            $tracks[] = (object)[
                                'id'       => $item['trackId'],
                                'titulo'   => $item['trackName'],
                                'artista'  => $item['artistName'],
                                'duracion' => sprintf('%d:%02d', $minutes, $seconds),
                            ];
                        }
                    }
                }

                return (object)[
                    'id'          => 'itunes_' . $collectionId,
                    'nombre'      => $collection['collectionName'],
                    'portada_url' => $highResImage,
                    'artista'     => $collection['artistName'],
                    'artista_id'  => null,
                    'descripcion' => 'Álbum lanzado en ' . substr($collection['releaseDate'] ?? '', 0, 4) . '. Disponible en Apple Music.',
                    'canciones'   => $tracks,
                    'apple_url'   => $collection['collectionViewUrl'] ?? '#',
                ];

            } catch (\Exception $e) {
                Log::error('ItunesService::getAlbumBySearch Error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get just the cover image URL for an artist + album name combination.
     */
    public function getCoverUrl(string $artist, string $albumName): ?string
    {
        $cacheKey = "itunes.cover." . md5($artist . $albumName);

        return Cache::remember($cacheKey, 86400, function () use ($artist, $albumName) {
            try {
                $response = Http::get("https://itunes.apple.com/search", [
                    'term'   => $artist . ' ' . $albumName,
                    'entity' => 'album',
                    'limit'  => 3,
                ]);

                if ($response->successful()) {
                    $results = $response->json()['results'] ?? [];
                    foreach ($results as $r) {
                        if (stripos($r['collectionName'], $albumName) !== false) {
                            return str_replace('100x100bb.jpg', '600x600bb.jpg', $r['artworkUrl100'] ?? '');
                        }
                    }
                    // fallback to first result
                    if (!empty($results)) {
                        return str_replace('100x100bb.jpg', '600x600bb.jpg', $results[0]['artworkUrl100'] ?? '');
                    }
                }
            } catch (\Exception $e) {
                Log::error('ItunesService::getCoverUrl Error: ' . $e->getMessage());
            }
            return null;
        });
    }
}
