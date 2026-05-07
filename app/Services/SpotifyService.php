<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class SpotifyService {
    private $clientId;
    private $clientSecret;
    private $accessToken;

    public function __construct($clientId = null, $clientSecret = null) {
        $this->clientId = $clientId ?: env('SPOTIFY_CLIENT_ID');
        $this->clientSecret = $clientSecret ?: env('SPOTIFY_CLIENT_SECRET');
        $this->authenticate();
    }

    private function authenticate() {
        $this->accessToken = Cache::remember('spotify_access_token', 3500, function () {
            $auth_url = 'https://accounts.spotify.com/api/token';
            $ch = curl_init($auth_url);
            
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)
                ],
                CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => 30
            ]);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code === 200) {
                $data = json_decode($response, true);
                return $data['access_token'] ?? null;
            }
            return null;
        });
    }

    private function request($url) {
        if (!$this->accessToken) {
            \Log::error('SpotifyService: No access token available');
            return null;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json'
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 30
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($http_code === 200) {
            return json_decode($response, true);
        }

        if ($http_code === 429) {
            \Log::warning("Spotify API: Rate Limited (429)");
            return ['error' => 'rate_limited', 'message' => 'Too many requests'];
        }

        \Log::error("SpotifyService request failed", [
            'url'       => $url,
            'http_code' => $http_code,
            'response'  => substr($response, 0, 500),
            'curl_error'=> $curl_error,
        ]);
        return null;
    }

    public function searchArtist($name) {
        $cacheKey = 'spotify.search.' . md5($name);
        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
            if (!isset($data['error'])) return $data;
            Cache::forget($cacheKey);
        }

        $query = urlencode($name);
        $url = "https://api.spotify.com/v1/search?q={$query}&type=artist&limit=1";
        $result = $this->request($url);
        
        if ($result && !isset($result['error'])) {
            $artist = $result['artists']['items'][0] ?? null;
            if ($artist) {
                Cache::put($cacheKey, $artist, 86400);
            }
            return $artist;
        }
        
        return $result; // Returns error array or null
    }

    public function getArtistTopTracks($artistId, $market = 'ES') {
        return Cache::remember("spotify.artist.{$artistId}.top_tracks.{$market}", 86400, function () use ($artistId, $market) {
            $url = "https://api.spotify.com/v1/artists/{$artistId}/top-tracks?market={$market}";
            $result = $this->request($url);
            return $result['tracks'] ?? [];
        });
    }

    public function getArtistAlbums($artistId, $limit = 5) {
        return Cache::remember("spotify.artist.{$artistId}.albums.{$limit}", 86400, function () use ($artistId, $limit) {
            $url = "https://api.spotify.com/v1/artists/{$artistId}/albums?include_groups=album&limit={$limit}";
            $result = $this->request($url);
            return $result['items'] ?? [];
        });
    }

    public function getAlbumTracks($albumId) {
        return Cache::remember("spotify.album.{$albumId}.tracks", 86400, function () use ($albumId) {
            $url = "https://api.spotify.com/v1/albums/{$albumId}/tracks?limit=50";
            $result = $this->request($url);
            return $result['items'] ?? [];
        });
    }

    public function getAlbum($albumId) {
        $url = "https://api.spotify.com/v1/albums/{$albumId}";
        return $this->request($url);
    }

    public function getTrack($trackId) {
        $url = "https://api.spotify.com/v1/tracks/{$trackId}";
        return $this->request($url);
    }

    public function getArtist($artistId) {
        $cacheKey = "spotify.artist.{$artistId}";
        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
            if (!isset($data['error'])) return $data;
            Cache::forget($cacheKey);
        }

        $url = "https://api.spotify.com/v1/artists/{$artistId}";
        $result = $this->request($url);

        if ($result && !isset($result['error'])) {
            Cache::put($cacheKey, $result, 86400);
        }
        
        return $result;
    }

    public function getAccessToken() {
        return $this->accessToken;
    }

    public function getPlaylistTracks($playlistId, $limit = 50) {
        return Cache::remember("spotify.playlist.{$playlistId}.tracks.{$limit}", 3600, function () use ($playlistId, $limit) {
            $url = "https://api.spotify.com/v1/playlists/{$playlistId}/tracks?limit={$limit}";
            $result = $this->request($url);
            return $result['items'] ?? [];
        });
    }

    public function getNewReleases($limit = 10, $country = 'ES') {
        return Cache::remember("spotify.browse.new-releases.{$country}.{$limit}", 3600, function () use ($limit, $country) {
            $url = "https://api.spotify.com/v1/browse/new-releases?country={$country}&limit={$limit}";
            $result = $this->request($url);
            return $result['albums']['items'] ?? [];
        });
    }

    /**
     * Get related artists for an artist.
     */
    public function getRelatedArtists(string $artistId): array {
        $url = "https://api.spotify.com/v1/artists/{$artistId}/related-artists";
        $result = $this->request($url);
        return $result['artists'] ?? [];
    }

    /**
     * Search for tracks by genre + keyword.
     */
    public function searchTracks(string $query, int $limit = 20): array {
        $encoded = urlencode($query);
        $url = "https://api.spotify.com/v1/search?q={$encoded}&type=track&limit={$limit}&market=ES";
        $result = $this->request($url);
        return $result['tracks']['items'] ?? [];
    }

    /**
     * Search for albums by genre + keyword.
     */
    public function searchAlbums(string $query, int $limit = 10): array {
        $encoded = urlencode($query);
        $url = "https://api.spotify.com/v1/search?q={$encoded}&type=album&limit={$limit}&market=ES";
        $result = $this->request($url);
        return $result['albums']['items'] ?? [];
    }

    /**
     * Get personalized recommendations using a weighted intersection approach.
     * Strategy:
     * 1. Include user selected tracks directly into the playlist.
     * 2. Include 1-2 tracks from the user selected albums into the playlist.
     * 3. Extract all artists from tracks/albums.
     * 4. Fill remaining playlist with top tracks from these artists.
     * 5. Recommend albums from these artists (different from selected).
     * 6. Recommend singles from these artists' top tracks.
     * 7. Fallback to genre searches if we need more items.
     */
    public function getRecommendations(array $genres = [], array $albumIds = [], array $trackIds = []): array
    {
        $resultAlbums = [];
        $resultSingles = [];
        $resultPlaylist = [];
        
        $seenTrackIds = [];
        $seenAlbumIds = array_flip($albumIds);
        $extractedArtists = []; // [id => name]

        // --- Step 1: Process selected tracks ---
        $selectedTracks = [];
        foreach (array_slice($trackIds, 0, 5) as $trackId) {
            $track = $this->getTrack($trackId);
            if ($track && !isset($track['error'])) {
                $selectedTracks[] = $track;
                $seenTrackIds[$trackId] = true;
                foreach ($track['artists'] ?? [] as $artist) {
                    $extractedArtists[$artist['id']] = $artist['name'];
                }
            }
        }

        // --- Step 2: Process selected albums ---
        $selectedAlbums = [];
        foreach (array_slice($albumIds, 0, 5) as $albumId) {
            $album = $this->getAlbum($albumId);
            if ($album && !isset($album['error'])) {
                $selectedAlbums[] = $album;
                foreach ($album['artists'] ?? [] as $artist) {
                    $extractedArtists[$artist['id']] = $artist['name'];
                }
            }
        }

        // --- Step 3: Start building the Playlist (Target ~15) ---
        // A) Include the selected tracks themselves
        foreach ($selectedTracks as $track) {
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

        // B) Include 1-2 tracks from the selected albums
        foreach ($selectedAlbums as $album) {
            $albumTracks = $this->getAlbumTracks($album['id']);
            $addedFromAlbum = 0;
            foreach ($albumTracks as $track) {
                if (!isset($seenTrackIds[$track['id']])) {
                    $seenTrackIds[$track['id']] = true;
                    $minutes = floor(($track['duration_ms'] ?? 0) / 60000);
                    $seconds = round((($track['duration_ms'] ?? 0) % 60000) / 1000);
                    
                    $resultPlaylist[] = [
                        'id'       => $track['id'],
                        'name'     => $track['name'],
                        'artist'   => $track['artists'][0]['name'] ?? 'Unknown',
                        'image'    => $album['images'][0]['url'] ?? null, // Use album image
                        'duration' => sprintf('%d:%02d', $minutes, $seconds),
                        'url'      => $track['external_urls']['spotify'] ?? '#',
                    ];
                    $addedFromAlbum++;
                    if ($addedFromAlbum >= 2) break; // Max 2 tracks per selected album
                }
            }
        }

        // C) Fill rest of playlist with Top Tracks from the extracted artists
        $artistIds = array_keys($extractedArtists);
        shuffle($artistIds); // mix up the artists
        foreach ($artistIds as $artistId) {
            if (count($resultPlaylist) >= 15) break;
            
            $topTracks = $this->getArtistTopTracks($artistId);
            foreach ($topTracks as $track) {
                if (!isset($seenTrackIds[$track['id']])) {
                    $seenTrackIds[$track['id']] = true;
                    $minutes = floor(($track['duration_ms'] ?? 0) / 60000);
                    $seconds = round((($track['duration_ms'] ?? 0) % 60000) / 1000);
                    $albumImg = $track['album']['images'][0]['url'] ?? null;
                    
                    $resultPlaylist[] = [
                        'id'       => $track['id'],
                        'name'     => $track['name'],
                        'artist'   => $track['artists'][0]['name'] ?? 'Unknown',
                        'image'    => $albumImg,
                        'duration' => sprintf('%d:%02d', $minutes, $seconds),
                        'url'      => $track['external_urls']['spotify'] ?? '#',
                    ];
                    break; // Just 1 top track per artist per loop for variety
                }
            }
        }

        // D) Fallback to genre search if still under 15
        if (count($resultPlaylist) < 15) {
            foreach (array_slice($genres, 0, 3) as $genre) {
                $tracks = $this->searchTracks($genre, 10);
                foreach ($tracks as $track) {
                    if (!isset($seenTrackIds[$track['id']])) {
                        $seenTrackIds[$track['id']] = true;
                        $minutes = floor(($track['duration_ms'] ?? 0) / 60000);
                        $seconds = round((($track['duration_ms'] ?? 0) % 60000) / 1000);
                        $albumImg = $track['album']['images'][0]['url'] ?? null;
                        
                        $resultPlaylist[] = [
                            'id'       => $track['id'],
                            'name'     => $track['name'],
                            'artist'   => $track['artists'][0]['name'] ?? 'Unknown',
                            'image'    => $albumImg,
                            'duration' => sprintf('%d:%02d', $minutes, $seconds),
                            'url'      => $track['external_urls']['spotify'] ?? '#',
                        ];
                        if (count($resultPlaylist) >= 15) break 2;
                    }
                }
            }
        }

        // --- Step 4: Recommended Albums (Target ~4) ---
        // A) Albums from the extracted artists
        foreach ($artistIds as $artistId) {
            if (count($resultAlbums) >= 4) break;
            
            $artistAlbums = $this->getArtistAlbums($artistId, 5);
            foreach ($artistAlbums as $album) {
                if (($album['album_type'] ?? '') !== 'album') continue; // Full albums only
                if (isset($seenAlbumIds[$album['id']])) continue;
                
                $seenAlbumIds[$album['id']] = true;
                $resultAlbums[] = [
                    'id'     => $album['id'],
                    'name'   => $album['name'],
                    'artist' => $album['artists'][0]['name'] ?? 'Unknown',
                    'image'  => $album['images'][0]['url'] ?? null,
                    'url'    => $album['external_urls']['spotify'] ?? '#',
                ];
                break; // One album per artist
            }
        }

        // B) Fallback to genre search if still under 4
        if (count($resultAlbums) < 4) {
            foreach (array_slice($genres, 0, 2) as $genre) {
                if (count($resultAlbums) >= 4) break;
                $searchAlbums = $this->searchAlbums($genre . ' 2024', 5);
                foreach ($searchAlbums as $album) {
                    if (isset($seenAlbumIds[$album['id']])) continue;
                    if (($album['album_type'] ?? '') !== 'album') continue;
                    $seenAlbumIds[$album['id']] = true;
                    
                    $resultAlbums[] = [
                        'id'     => $album['id'],
                        'name'   => $album['name'],
                        'artist' => $album['artists'][0]['name'] ?? 'Unknown',
                        'image'  => $album['images'][0]['url'] ?? null,
                        'url'    => $album['external_urls']['spotify'] ?? '#',
                    ];
                    if (count($resultAlbums) >= 4) break;
                }
            }
        }


        // --- Step 5: Recommended Singles (Target ~4) ---
        // A) Top tracks from artists (in reverse so we don't pick the same as playlist)
        foreach ($artistIds as $artistId) {
            if (count($resultSingles) >= 4) break;
            
            $topTracks = array_reverse($this->getArtistTopTracks($artistId)); 
            foreach ($topTracks as $track) {
                if (!isset($seenTrackIds[$track['id']])) {
                    $seenTrackIds[$track['id']] = true;
                    $minutes = floor(($track['duration_ms'] ?? 0) / 60000);
                    $seconds = round((($track['duration_ms'] ?? 0) % 60000) / 1000);
                    $albumImg = $track['album']['images'][0]['url'] ?? null;
                    
                    $resultSingles[] = [
                        'id'       => $track['id'],
                        'name'     => $track['name'],
                        'artist'   => $track['artists'][0]['name'] ?? 'Unknown',
                        'image'    => $albumImg,
                        'duration' => sprintf('%d:%02d', $minutes, $seconds),
                        'url'      => $track['external_urls']['spotify'] ?? '#',
                    ];
                    break;
                }
            }
        }

        // B) Fallback to genre search if still under 4
        if (count($resultSingles) < 4) {
             foreach (array_slice($genres, 0, 2) as $genre) {
                 if (count($resultSingles) >= 4) break;
                 $searchTracks = $this->searchTracks($genre . ' new release', 5);
                 foreach ($searchTracks as $track) {
                    if (isset($seenTrackIds[$track['id']])) continue;
                    $seenTrackIds[$track['id']] = true;
                    
                    $minutes = floor(($track['duration_ms'] ?? 0) / 60000);
                    $seconds = round((($track['duration_ms'] ?? 0) % 60000) / 1000);
                    $albumImg = $track['album']['images'][0]['url'] ?? null;
                    
                    $resultSingles[] = [
                        'id'       => $track['id'],
                        'name'     => $track['name'],
                        'artist'   => $track['artists'][0]['name'] ?? 'Unknown',
                        'image'    => $albumImg,
                        'duration' => sprintf('%d:%02d', $minutes, $seconds),
                        'url'      => $track['external_urls']['spotify'] ?? '#',
                    ];
                    if (count($resultSingles) >= 4) break;
                 }
             }
        }

        return [
            'recommended_albums'  => $resultAlbums,
            'recommended_singles' => $resultSingles,
            'weekly_playlist'     => $resultPlaylist,
        ];
    }
}
