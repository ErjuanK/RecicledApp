<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlbumController extends Controller
{
    private $spotify;
    
    public function __construct()
    {
        $clientId = config('services.spotify.client_id');
        $clientSecret = config('services.spotify.client_secret');
        $this->spotify = new \App\Services\SpotifyService($clientId, $clientSecret);
    }
    
    public function show($id)
    {
        // 1. Try Local DB (if numeric)
        if (is_numeric($id)) {
            $localAlbum = \App\Models\Album::with('artista', 'canciones')->find($id);

            if ($localAlbum) {
                $album = (object)[
                    'id' => $localAlbum->album_id,
                    'nombre' => $localAlbum->titulo,
                    'portada_url' => $localAlbum->portada_url ? asset($localAlbum->portada_url) : asset('multimedia/img/default-album.jpg'),
                    'artista' => $localAlbum->artista->nombre_artistico,
                    'artista_id' => $localAlbum->artista_id,
                    'descripcion' => "Álbum lanzado en " . ($localAlbum->fecha_lanzamiento ? substr($localAlbum->fecha_lanzamiento, 0, 4) : 'Unknown') . ". " . ($localAlbum->contexto ?? 'Sin descripción.'),
                    'canciones' => []
                ];

                foreach ($localAlbum->canciones as $cancion) {
                    $min = floor($cancion->duracion / 60);
                    $sec = $cancion->duracion % 60;

                    $album->canciones[] = (object)[
                        'id' => $cancion->cancion_id,
                        'titulo' => $cancion->titulo,
                        'artista' => $localAlbum->artista->nombre_artistico,
                        'duracion' => sprintf("%d:%02d", $min, $sec)
                    ];
                }

                return view('album', compact('album'));
            }
        }

        // 2. Fallback to Spotify
        $albumData = $this->spotify->getAlbum($id);
        
        if ($albumData && !isset($albumData['error'])) {
            
            $album = (object)[
                'id' => $albumData['id'],
                'nombre' => $albumData['name'],
                'portada_url' => $albumData['images'][0]['url'] ?? asset('multimedia/img/default-album.jpg'),
                'artista' => $albumData['artists'][0]['name'],
                'artista_id' => $albumData['artists'][0]['id'],
                'descripcion' => "Álbum lanzado en " . substr($albumData['release_date'], 0, 4) . ". " . ucfirst($albumData['album_type']) . " oficial.",
                'canciones' => []
            ];
            
            if (isset($albumData['tracks']['items'])) {
                foreach ($albumData['tracks']['items'] as $track) {
                    $minutes = floor($track['duration_ms'] / 60000);
                    $seconds = ($track['duration_ms'] % 60000) / 1000;
                    
                    $album->canciones[] = (object)[
                        'id' => $track['id'],
                        'titulo' => $track['name'],
                        'artista' => $track['artists'][0]['name'],
                        'duracion' => sprintf("%d:%02d", $minutes, $seconds)
                    ];
                }
            }
            return view('album', compact('album'));
        }

        // 3. Last Resort: Hardcoded Fallbacks for Carousel Albums (to avoid 404 on front page)
        $fallbacks = [
            '0U28P0QVB1QRxpqp5IHOlH' => [
                'name' => 'CHROMAKOPIA',
                'artist' => 'Tyler, the Creator',
                'artist_id' => '4V8LLpRMTZpC3bZ96I7a9G', // Tyler's real ID
                'image' => asset('multimedia/img/Portadas/album/cromakopia - Tyler the creator.png'),
                'desc' => 'Chromakopia is the seventh studio album by American rapper Tyler, the Creator.'
            ],
            '3SUEJULSGgBDG1j4GQhfYY' => [
                'name' => 'LUX',
                'artist' => 'Rosalia',
                'artist_id' => '7ltDVBr6mKbRvohxheJ9h1',
                'image' => asset('multimedia/img/Portadas/album/rosalia-lux.webp'),
                'desc' => 'Un album que fusiona flamenco con ritmos urbanos.'
            ],
            '5K79FLRUCSysQnVESLcTdb' => [
                'name' => 'Debi Tirar Mas Fotos',
                'artist' => 'Bad Bunny',
                'artist_id' => '4q3ewBCX7sLwd24euuV69X',
                'image' => asset('multimedia/img/Portadas/album/dtmf - bad bunny.png'),
                'desc' => 'Regreso a las raíces del trap del artista.'
            ]
        ];

        if (isset($fallbacks[$id])) {
            $f = $fallbacks[$id];
            $album = (object)[
                'id' => $id,
                'nombre' => $f['name'],
                'portada_url' => $f['image'],
                'artista' => $f['artist'],
                'artista_id' => $f['artist_id'],
                'descripcion' => $f['desc'],
                'canciones' => []
            ];
            return view('album', compact('album'));
        }

        if (isset($albumData['error']) && $albumData['error'] === 'rate_limited') {
            abort(429, 'Demasiadas peticiones a la API de Spotify. Por favor, inténtalo de nuevo en unos minutos.');
        }

        abort(404, 'Álbum no encontrado');
    }
}
