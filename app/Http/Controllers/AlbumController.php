<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlbumController extends Controller
{
    private $spotify;
    
    public function __construct()
    {
        $clientId = 'cf0a28b6c1c9425bbfb697f9a072afc8';
        $clientSecret = '16c9bcf6476e47138c1adc87c82596ea';
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
        } else {
            abort(404, 'Álbum no encontrado');
        }
    }
}
