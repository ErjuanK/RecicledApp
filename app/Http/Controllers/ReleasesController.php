<?php

namespace App\Http\Controllers;

use App\Services\ItunesService;
use App\Services\LastFmService;
use App\Services\EditorialApiService;
use App\Models\UserLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ReleasesController extends Controller
{
    private ItunesService $itunes;
    private LastFmService $lastFm;
    private EditorialApiService $editorial;

    public function __construct(ItunesService $itunes, LastFmService $lastFm, EditorialApiService $editorial)
    {
        $this->itunes   = $itunes;
        $this->lastFm   = $lastFm;
        $this->editorial = $editorial;
    }

    public function index()
    {
        $personalizedReleases = [];
        $likedArtistsNames    = [];

        // ── 1. Build personalised feed for logged-in users ──────────────────
        if (Auth::check()) {
            $userId   = Auth::id();
            $cacheKey = "releases.personalized.{$userId}";

            // Cache per-user for 6 hours so it feels fresh but doesn't hammer APIs
            $personalizedReleases = Cache::remember($cacheKey, 3600 * 6, function () use (&$likedArtistsNames) {

                $userLikes = UserLike::where('user_id', Auth::id())->get();
                $seen      = [];
                $results   = [];

                // Extract unique artist names from all liked items
                foreach ($userLikes as $like) {
                    if ($like->type === 'artist' && !empty($like->name)) {
                        $likedArtistsNames[] = $like->name;
                    } elseif (!empty($like->artist_name)) {
                        $likedArtistsNames[] = $like->artist_name;
                    }
                }
                $likedArtistsNames = array_unique($likedArtistsNames);

                if (empty($likedArtistsNames)) return [];

                // Pick up to 3 liked artists randomly as "seeds"
                $seeds = array_slice(collect($likedArtistsNames)->shuffle()->toArray(), 0, 3);

                $currentYear = date('Y');

                foreach ($seeds as $seedArtist) {
                    // A) Albums from the liked artist itself (any recent release)
                    $directAlbums = $this->itunes->searchAlbums($seedArtist, 10);
                    foreach ($directAlbums as $album) {
                        $releaseYear = substr($album['release_date'] ?? '', 0, 4);
                        if ($releaseYear !== $currentYear) continue;

                        $key = strtolower($album['name'] . '|' . ($album['artists'][0]['name'] ?? ''));
                        if (!isset($seen[$key]) && count($results) < 8) {
                            $seen[$key] = true;
                            $album['reason'] = "Porque te gusta {$seedArtist}";
                            $results[] = $album;
                        }
                    }

                    // B) Find similar artists via Last.fm
                    $similarArtists = $this->lastFm->getSimilarArtists($seedArtist, 4);

                    foreach (array_slice($similarArtists, 0, 3) as $similarArtist) {
                        if (count($results) >= 8) break 2;

                        $similarAlbums = $this->itunes->searchAlbums($similarArtist, 8);
                        foreach ($similarAlbums as $album) {
                            $releaseYear = substr($album['release_date'] ?? '', 0, 4);
                            if ($releaseYear !== $currentYear) continue;

                            $key = strtolower($album['name'] . '|' . ($album['artists'][0]['name'] ?? ''));
                            if (!isset($seen[$key]) && count($results) < 8) {
                                $seen[$key] = true;
                                $album['reason'] = "Porque te gusta {$seedArtist}";
                                $results[] = $album;
                            }
                        }
                    }
                }

                return $results;
            });

            // Rebuild likedArtistsNames for the view subtitle (always fresh)
            $userLikes = UserLike::where('user_id', $userId)->get();
            foreach ($userLikes as $like) {
                if ($like->type === 'artist' && !empty($like->name)) {
                    $likedArtistsNames[] = $like->name;
                } elseif (!empty($like->artist_name)) {
                    $likedArtistsNames[] = $like->artist_name;
                }
            }
            $likedArtistsNames = array_unique($likedArtistsNames);
        }

        // ── 2. Editorial section ─────────────────────────────────────────────
        $upcomingReleases        = $this->editorial->getUpcomingReleases();
        $recentEditorialReleases = $this->editorial->getRecentEditorialReleases();

        // Enrich with real iTunes covers (skip if already has cover_url from the weekly cache)
        foreach ($recentEditorialReleases as &$release) {
            if (empty($release['cover_url'])) {
                $release['cover_url'] = $this->itunes->getCoverUrl(
                    $release['itunes_artist'] ?? $release['artist'],
                    $release['itunes_album']  ?? $release['title']
                );
            }
        }
        unset($release);

        return view('releases.index', compact(
            'personalizedReleases',
            'likedArtistsNames',
            'upcomingReleases',
            'recentEditorialReleases'
        ));
    }
}
