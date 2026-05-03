<?php

namespace App\Http\Controllers;

use App\Services\SpotifyService;
use App\Services\EditorialApiService;
use App\Models\UserLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReleasesController extends Controller
{
    private SpotifyService $spotify;
    private EditorialApiService $editorial;

    public function __construct(SpotifyService $spotify, EditorialApiService $editorial)
    {
        $this->spotify = $spotify;
        $this->editorial = $editorial;
    }

    public function index()
    {
        $currentYear = date('Y');
        
        $personalizedReleases = [];
        $likedArtistsNames = [];

        // 1. If authenticated, fetch likes and try to get personalized releases
        if (Auth::check()) {
            $userLikes = UserLike::where('user_id', Auth::id())->inRandomOrder()->get();
            
            // Extract unique artist names from likes
            foreach ($userLikes as $like) {
                if ($like->type === 'artist' && !empty($like->name)) {
                    $likedArtistsNames[] = $like->name;
                } elseif (!empty($like->artist_name)) {
                    $likedArtistsNames[] = $like->artist_name;
                }
            }
            
            $likedArtistsNames = array_unique($likedArtistsNames);
            
            // Pick a couple of artists randomly to search for recent albums
            if (count($likedArtistsNames) > 0) {
                shuffle($likedArtistsNames);
                $searchArtists = array_slice($likedArtistsNames, 0, 3);
                
                foreach ($searchArtists as $artistName) {
                    // Search for albums by this artist, newest first ideally
                    $albums = $this->spotify->searchAlbums($artistName . ' tag:new', 2);
                    if (empty($albums)) {
                        $albums = $this->spotify->searchAlbums($artistName, 2);
                    }
                    
                    foreach ($albums as $album) {
                        // Avoid duplicates and filter by current year
                        $exists = array_filter($personalizedReleases, fn($r) => $r['id'] === $album['id']);
                        if (!$exists && str_starts_with($album['release_date'] ?? '', (string)$currentYear)) {
                            $personalizedReleases[] = $album;
                        }
                    }
                }
            }
        }

        // Get Upcoming Releases
        $upcomingReleases = $this->editorial->getUpcomingReleases();

        // Get Recent Editorial Releases
        $recentEditorialReleases = $this->editorial->getRecentEditorialReleases();

        return view('releases.index', compact('personalizedReleases', 'likedArtistsNames', 'upcomingReleases', 'recentEditorialReleases'));
    }
}
