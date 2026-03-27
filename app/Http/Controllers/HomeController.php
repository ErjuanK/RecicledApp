<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SpotifyService;

class HomeController extends Controller
{
    protected $spotify;

    public function __construct(SpotifyService $spotify)
    {
        $this->spotify = $spotify;
    }

    public function index(Request $request)
    {
        $genre = $request->get('genre');
        
        // 1. Fetch Top Tracks
        if ($genre) {
            $tracksData = $this->spotify->searchTracks('genre:"' . $genre . '"', 20);
            $tracks = array_slice($tracksData, 0, 5);
        } else {
            // Utilizamos una búsqueda general de éxitos recientes de España si no hay género
            $tracksData = $this->spotify->searchTracks('year:' . date('Y') . '', 20);
            $tracks = array_slice($tracksData, 0, 5);
        }

        // 2. Fetch Albums para la cuadrícula (exactamente 3)
        $albumsData = [];
        if ($genre && !empty($tracksData)) {
            // La API de Spotify no soporta el filtro 'genre:' directamente para álbumes.
            // Solución impecable: Extraemos los álbumes de las pistas top obtenidas para ese género.
            $seenAlbumIds = [];
            foreach ($tracksData as $t) {
                if (isset($t['album']) && $t['album']['album_type'] === 'album') {
                    if (!in_array($t['album']['id'], $seenAlbumIds)) {
                        $seenAlbumIds[] = $t['album']['id'];
                        $albumsData[] = $t['album'];
                    }
                }
                if (count($albumsData) >= 3) break;
            }
        }

        // Fallback: Si no hay género seleccionado o no logramos extraer 3 álbumes del género
        if (count($albumsData) < 3) {
            $albumsData = $this->spotify->getNewReleases(20, 'ES');
        }
        
        $albums = [];
        foreach ($albumsData as $album) {
            if ($album['album_type'] === 'album') {
                // Obtener géneros del artista
                $artistId = $album['artists'][0]['id'] ?? null;
                if ($artistId) {
                    $artistData = $this->spotify->getArtist($artistId);
                    $album['genres'] = $artistData['genres'] ?? [];
                } else {
                    $album['genres'] = [];
                }
                $albums[] = $album;
            }
            if (count($albums) >= 3) break;
        }

        // Fallback: si no hay álbumes completos, tomamos los primeros
        if (count($albums) < 3) {
            foreach ($albumsData as $album) {
                if (count($albums) >= 3) break;
                if (!in_array($album, $albums)) {
                    $album['genres'] = [];
                    $albums[] = $album;
                }
            }
        }

        return view('home', compact('tracks', 'albums', 'genre'));
    }
}
