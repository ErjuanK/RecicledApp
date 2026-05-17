<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ItunesService
{
    protected $spotify;

    public function __construct()
    {
        $clientId = config('services.spotify.client_id');
        $clientSecret = config('services.spotify.client_secret');
        $this->spotify = new SpotifyService($clientId, $clientSecret);
    }

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
                            'artists'       => [['id' => $result['artistId'] ?? null, 'name' => $result['artistName']]],
                            'images'        => [['url' => $highResImage]],
                            'release_date'  => $result['releaseDate'] ?? now()->toIso8601String(),
                            'album_type'    => 'album',
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

    /**
     * Search for tracks by keyword.
     */
    public function searchTracks(string $query, int $limit = 20): array
    {
        $cacheKey = "itunes.search.tracks." . md5($query . $limit);

        return Cache::remember($cacheKey, 86400, function () use ($query, $limit) {
            try {
                // If it's a genre search from Discovery, iTunes handles "term" best.
                $cleanQuery = str_replace(['genre:"', 'genre:', '"', 'year:', 'new release'], ['', '', '', '', ''], $query);
                
                $response = Http::get("https://itunes.apple.com/search", [
                    'term'   => $cleanQuery,
                    'entity' => 'song',
                    'limit'  => $limit
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $mappedResults = [];

                    foreach ($data['results'] ?? [] as $result) {
                        $highResImage = str_replace('100x100bb.jpg', '600x600bb.jpg', $result['artworkUrl100'] ?? '');
                        $mappedResults[] = [
                            'id'            => $result['trackId'] ?? '',
                            'name'          => $result['trackName'] ?? '',
                            'artists'       => [['id' => $result['artistId'] ?? null, 'name' => $result['artistName'] ?? 'Unknown']],
                            'album'         => [
                                'id' => $result['collectionId'] ?? null,
                                'name' => $result['collectionName'] ?? '',
                                'images' => [['url' => $highResImage]],
                                'album_type' => 'album'
                            ],
                            'duration_ms'   => $result['trackTimeMillis'] ?? 0,
                            'popularity'    => 50,
                            'external_urls' => ['spotify' => $result['trackViewUrl'] ?? '#']
                        ];
                    }
                    return $mappedResults;
                }
            } catch (\Exception $e) {
                Log::error('ItunesService::searchTracks Error: ' . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Search for artist by keyword.
     */
    public function searchArtist(string $name): ?array
    {
        $cacheKey = 'itunes.search.artist.' . md5($name);

        return Cache::remember($cacheKey, 86400, function () use ($name) {
            try {
                $response = Http::get("https://itunes.apple.com/search", [
                    'term'   => $name,
                    'entity' => 'musicArtist',
                    'limit'  => 1
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['results'])) {
                        $result = $data['results'][0];
                        
                        // Default Fallbacks
                        $imageUrl = asset('multimedia/img/default-artist.jpg');
                        $followers = rand(1000, 1000000);
                        $genres = [$result['primaryGenreName'] ?? 'Music'];
                        
                        // Try to enrich with Spotify Data
                        $spotifyData = $this->spotify->searchArtist($result['artistName']);
                        if ($spotifyData && !isset($spotifyData['error'])) {
                            if (!empty($spotifyData['images'])) {
                                $imageUrl = $spotifyData['images'][0]['url'];
                            }
                            if (isset($spotifyData['followers']['total'])) {
                                $followers = $spotifyData['followers']['total'];
                            }
                            if (!empty($spotifyData['genres'])) {
                                $genres = $spotifyData['genres'];
                            }
                        } else {
                            // Fallback to iTunes song search for an image if Spotify fails
                            $songResp = Http::get("https://itunes.apple.com/search", ['term' => $name, 'entity' => 'song', 'limit' => 1]);
                            if ($songResp->successful() && !empty($songResp->json()['results'])) {
                                $imageUrl = str_replace('100x100bb.jpg', '600x600bb.jpg', $songResp->json()['results'][0]['artworkUrl100'] ?? $imageUrl);
                            }
                        }

                        return [
                            'id' => $result['artistId'],
                            'name' => $result['artistName'],
                            'genres' => $genres,
                            'followers' => ['total' => $followers],
                            'images' => [
                                ['url' => $imageUrl],
                                ['url' => $imageUrl]
                            ],
                            'external_urls' => ['spotify' => $result['artistLinkUrl'] ?? '#']
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::error('ItunesService::searchArtist Error: ' . $e->getMessage());
            }
            return null;
        });
    }

    /**
     * Get Artist by ID.
     */
    public function getArtist($artistId): ?array
    {
        if (preg_match('/^[a-zA-Z0-9]{22}$/', $artistId)) {
            // It's a Spotify ID, we can't look it up in iTunes easily.
            // But we can try to search the string just in case it's a name disguised as an ID?
            // Usually we return error for invalid iTunes ID format to force fallback.
            return ['error' => 'not_found'];
        }

        $cacheKey = "itunes.artist.{$artistId}";

        return Cache::remember($cacheKey, 86400, function () use ($artistId) {
            try {
                $response = Http::get("https://itunes.apple.com/lookup", [
                    'id' => $artistId
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['results'])) {
                        $result = $data['results'][0];
                        
                        // Default Fallbacks
                        $imageUrl = asset('multimedia/img/default-artist.jpg');
                        $followers = rand(1000, 1000000);
                        $genres = [$result['primaryGenreName'] ?? 'Music'];
                        
                        // Try to enrich with Spotify Data
                        $spotifyData = $this->spotify->searchArtist($result['artistName']);
                        if ($spotifyData && !isset($spotifyData['error'])) {
                            if (!empty($spotifyData['images'])) {
                                $imageUrl = $spotifyData['images'][0]['url'];
                            }
                            if (isset($spotifyData['followers']['total'])) {
                                $followers = $spotifyData['followers']['total'];
                            }
                            if (!empty($spotifyData['genres'])) {
                                $genres = $spotifyData['genres'];
                            }
                        } else {
                            $songResp = Http::get("https://itunes.apple.com/search", ['term' => $result['artistName'], 'entity' => 'song', 'limit' => 1]);
                            if ($songResp->successful() && !empty($songResp->json()['results'])) {
                                $imageUrl = str_replace('100x100bb.jpg', '600x600bb.jpg', $songResp->json()['results'][0]['artworkUrl100'] ?? $imageUrl);
                            }
                        }

                        return [
                            'id' => $result['artistId'],
                            'name' => $result['artistName'],
                            'genres' => $genres,
                            'followers' => ['total' => $followers],
                            'images' => [
                                ['url' => $imageUrl],
                                ['url' => $imageUrl]
                            ],
                            'external_urls' => ['spotify' => $result['artistLinkUrl'] ?? '#']
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::error('ItunesService::getArtist Error: ' . $e->getMessage());
            }
            return ['error' => 'not_found'];
        });
    }

    /**
     * Get Artist Top Tracks.
     */
    public function getArtistTopTracks($artistId): array
    {
        $cacheKey = "itunes.artist.{$artistId}.top_tracks";

        return Cache::remember($cacheKey, 86400, function () use ($artistId) {
            try {
                $response = Http::get("https://itunes.apple.com/lookup", [
                    'id' => $artistId,
                    'entity' => 'song',
                    'limit' => 10
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $mappedResults = [];

                    foreach ($data['results'] ?? [] as $result) {
                        if (($result['wrapperType'] ?? '') !== 'track') continue;
                        
                        $highResImage = str_replace('100x100bb.jpg', '600x600bb.jpg', $result['artworkUrl100'] ?? '');
                        $mappedResults[] = [
                            'id'            => $result['trackId'],
                            'name'          => $result['trackName'],
                            'artists'       => [['id' => $result['artistId'] ?? null, 'name' => $result['artistName']]],
                            'album'         => [
                                'id' => $result['collectionId'],
                                'name' => $result['collectionName'],
                                'images' => [['url' => $highResImage]],
                                'album_type' => 'album'
                            ],
                            'duration_ms'   => $result['trackTimeMillis'] ?? 0,
                            'popularity'    => 50,
                            'external_urls' => ['spotify' => $result['trackViewUrl'] ?? '#']
                        ];
                    }
                    return $mappedResults;
                }
            } catch (\Exception $e) {
                Log::error('ItunesService::getArtistTopTracks Error: ' . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Get Artist Albums.
     */
    public function getArtistAlbums($artistId, $limit = 5): array
    {
        $cacheKey = "itunes.artist.{$artistId}.albums.{$limit}";

        return Cache::remember($cacheKey, 86400, function () use ($artistId, $limit) {
            try {
                // Fetch more than needed because we will filter out singles
                $response = Http::get("https://itunes.apple.com/lookup", [
                    'id' => $artistId,
                    'entity' => 'album',
                    'limit' => 30
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $mappedResults = [];

                    foreach ($data['results'] ?? [] as $result) {
                        if (($result['wrapperType'] ?? '') !== 'collection') continue;
                        
                        $name = $result['collectionName'] ?? '';
                        $trackCount = $result['trackCount'] ?? 1;

                        // Filter out Singles, EPs and Remixes
                        if ($trackCount < 4) continue;
                        if (stripos($name, ' - Single') !== false) continue;
                        if (stripos($name, ' - EP') !== false) continue;
                        if (stripos($name, ' Remix') !== false) continue;

                        $highResImage = str_replace('100x100bb.jpg', '600x600bb.jpg', $result['artworkUrl100'] ?? '');
                        $mappedResults[] = [
                            'id'            => $result['collectionId'],
                            'name'          => $result['collectionName'],
                            'artists'       => [['id' => $result['artistId'] ?? null, 'name' => $result['artistName']]],
                            'images'        => [['url' => $highResImage]],
                            'release_date'  => $result['releaseDate'] ?? now()->toIso8601String(),
                            'album_type'    => 'album',
                            'total_tracks'  => $result['trackCount'] ?? 1,
                            'external_urls' => ['spotify' => $result['collectionViewUrl'] ?? '#']
                        ];

                        if (count($mappedResults) >= $limit) {
                            break;
                        }
                    }
                    return $mappedResults;
                }
            } catch (\Exception $e) {
                Log::error('ItunesService::getArtistAlbums Error: ' . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Get Album by ID.
     */
    public function getAlbum($albumId): ?array
    {
        if (preg_match('/^[a-zA-Z0-9]{22}$/', $albumId)) {
            return ['error' => 'not_found'];
        }

        $cacheKey = "itunes.album.{$albumId}";

        return Cache::remember($cacheKey, 86400, function () use ($albumId) {
            try {
                $response = Http::get("https://itunes.apple.com/lookup", [
                    'id' => $albumId,
                    'entity' => 'song' // Fetches the album + its tracks
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $results = $data['results'] ?? [];
                    if (empty($results)) return ['error' => 'not_found'];

                    $albumData = $results[0]; // Collection info
                    $tracksData = array_slice($results, 1); // Tracks

                    $highResImage = str_replace('100x100bb.jpg', '600x600bb.jpg', $albumData['artworkUrl100'] ?? '');

                    $mappedTracks = [];
                    foreach ($tracksData as $item) {
                        if (($item['wrapperType'] ?? '') === 'track') {
                            $mappedTracks[] = [
                                'id' => $item['trackId'],
                                'name' => $item['trackName'],
                                'artists' => [['id' => $item['artistId'] ?? null, 'name' => $item['artistName']]],
                                'duration_ms' => $item['trackTimeMillis'] ?? 0,
                            ];
                        }
                    }

                    return [
                        'id' => $albumData['collectionId'],
                        'name' => $albumData['collectionName'],
                        'artists' => [['id' => $albumData['artistId'] ?? null, 'name' => $albumData['artistName']]],
                        'images' => [['url' => $highResImage]],
                        'release_date' => $albumData['releaseDate'] ?? now()->toIso8601String(),
                        'album_type' => 'album',
                        'tracks' => ['items' => $mappedTracks],
                        'external_urls' => ['spotify' => $albumData['collectionViewUrl'] ?? '#']
                    ];
                }
            } catch (\Exception $e) {
                Log::error('ItunesService::getAlbum Error: ' . $e->getMessage());
            }
            return ['error' => 'not_found'];
        });
    }

    /**
     * Get Album Tracks by ID.
     */
    public function getAlbumTracks($albumId): array
    {
        $album = $this->getAlbum($albumId);
        return $album['tracks']['items'] ?? [];
    }

    /**
     * Get Track by ID.
     */
    public function getTrack($trackId): ?array
    {
        if (preg_match('/^[a-zA-Z0-9]{22}$/', $trackId)) {
            return ['error' => 'not_found'];
        }

        $cacheKey = "itunes.track.{$trackId}";

        return Cache::remember($cacheKey, 86400, function () use ($trackId) {
            try {
                $response = Http::get("https://itunes.apple.com/lookup", [
                    'id' => $trackId
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['results'])) {
                        $result = $data['results'][0];
                        $highResImage = str_replace('100x100bb.jpg', '600x600bb.jpg', $result['artworkUrl100'] ?? '');

                        return [
                            'id'            => $result['trackId'],
                            'name'          => $result['trackName'],
                            'artists'       => [['id' => $result['artistId'] ?? null, 'name' => $result['artistName']]],
                            'album'         => [
                                'id' => $result['collectionId'],
                                'name' => $result['collectionName'],
                                'images' => [['url' => $highResImage]],
                                'album_type' => 'album'
                            ],
                            'duration_ms'   => $result['trackTimeMillis'] ?? 0,
                            'popularity'    => 50,
                            'external_urls' => ['spotify' => $result['trackViewUrl'] ?? '#']
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::error('ItunesService::getTrack Error: ' . $e->getMessage());
            }
            return ['error' => 'not_found'];
        });
    }

    /**
     * Get New Releases (mocks Spotify's browse/new-releases endpoint).
     */
    public function getNewReleases(int $limit = 10, string $country = 'es'): array
    {
        $cacheKey = "itunes.new_releases.{$country}.{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($limit, $country) {
            try {
                // iTunes RSS feed for new releases
                $response = Http::get("https://itunes.apple.com/{$country}/rss/topalbums/limit={$limit}/json");

                if ($response->successful()) {
                    $data = $response->json();
                    $mappedResults = [];

                    foreach ($data['feed']['entry'] ?? [] as $entry) {
                        $images = $entry['im:image'] ?? [];
                        $imageUrl = !empty($images) ? $images[count($images)-1]['label'] : asset('multimedia/img/default-album.jpg');
                        $imageUrl = str_replace('170x170bb.png', '600x600bb.jpg', $imageUrl); // Attempt high res

                        $mappedResults[] = [
                            'id'            => $entry['id']['attributes']['im:id'] ?? '',
                            'name'          => $entry['im:name']['label'] ?? '',
                            'artists'       => [['name' => $entry['im:artist']['label'] ?? 'Unknown']],
                            'images'        => [['url' => $imageUrl]],
                            'release_date'  => $entry['im:releaseDate']['label'] ?? now()->toIso8601String(),
                            'album_type'    => 'album',
                            'total_tracks'  => $entry['im:itemCount']['label'] ?? 1,
                            'external_urls' => ['spotify' => $entry['link']['attributes']['href'] ?? '#']
                        ];
                    }
                    return $mappedResults;
                }
            } catch (\Exception $e) {
                Log::error('ItunesService::getNewReleases Error: ' . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Get personalized recommendations using a weighted intersection approach.
     */
    public function getRecommendations(array $genres = [], array $albumIds = [], array $trackIds = []): array
    {
        // Simple mock since iTunes doesn't have an intelligent recommendation endpoint.
        $resultAlbums = [];
        $resultSingles = [];
        $resultPlaylist = [];
        
        // Use generic search based on genres or a default term if empty
        $query = !empty($genres) ? implode(' ', array_slice($genres, 0, 2)) : 'pop';
        
        $tracks = $this->searchTracks($query, 15);
        foreach ($tracks as $track) {
            $minutes = floor(($track['duration_ms'] ?? 0) / 60000);
            $seconds = round((($track['duration_ms'] ?? 0) % 60000) / 1000);
            
            $resultPlaylist[] = [
                'id'       => $track['id'],
                'name'     => $track['name'],
                'artist'   => $track['artists'][0]['name'] ?? 'Unknown',
                'image'    => $track['album']['images'][0]['url'] ?? null,
                'duration' => sprintf('%d:%02d', $minutes, $seconds),
                'url'      => $track['external_urls']['spotify'] ?? '#',
            ];
        }

        $albums = $this->searchAlbums($query, 4);
        foreach ($albums as $album) {
            $resultAlbums[] = [
                'id'     => $album['id'],
                'name'   => $album['name'],
                'artist' => $album['artists'][0]['name'] ?? 'Unknown',
                'image'  => $album['images'][0]['url'] ?? null,
                'url'    => $album['external_urls']['spotify'] ?? '#',
            ];
        }

        $singles = $this->searchTracks($query . ' remix', 4);
        foreach ($singles as $track) {
            $minutes = floor(($track['duration_ms'] ?? 0) / 60000);
            $seconds = round((($track['duration_ms'] ?? 0) % 60000) / 1000);
            
            $resultSingles[] = [
                'id'       => $track['id'],
                'name'     => $track['name'],
                'artist'   => $track['artists'][0]['name'] ?? 'Unknown',
                'image'    => $track['album']['images'][0]['url'] ?? null,
                'duration' => sprintf('%d:%02d', $minutes, $seconds),
                'url'      => $track['external_urls']['spotify'] ?? '#',
            ];
        }

        return [
            'recommended_albums'  => $resultAlbums,
            'recommended_singles' => $resultSingles,
            'weekly_playlist'     => $resultPlaylist,
        ];
    }
}
