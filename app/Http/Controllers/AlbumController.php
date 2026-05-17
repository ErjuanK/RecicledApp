<?php

namespace App\Http\Controllers;

use App\Services\ItunesService;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    private $itunes;
    
    public function __construct(ItunesService $itunes)
    {
        $this->itunes = $itunes;
    }
    
    public function show($id)
    {
        $likedAlbums = [];
        $likedSongs = [];
        if (\Illuminate\Support\Facades\Auth::check()) {
            $likes = \App\Models\UserLike::where('user_id', \Illuminate\Support\Facades\Auth::id())->get();
            $likedAlbums = $likes->where('type', 'album')->pluck('spotify_id')->toArray();
            $likedSongs = $likes->where('type', 'song')->pluck('spotify_id')->toArray();
        }
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

                return view('album', compact('album', 'likedAlbums', 'likedSongs'));
            }
        }

        // 2. Fallback to iTunes
        $albumData = $this->itunes->getAlbum($id);
        
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
            return view('album', compact('album', 'likedAlbums', 'likedSongs'));
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
            ],
            '7a8QhNYgKmcauIKB7rCyR5' => [
                'name' => 'El Baifo',
                'artist' => 'Quevedo',
                'artist_id' => '52iwsT98xCoGgiGntTiR7K',
                'image' => 'https://picsum.photos/seed/quevedobaifo/800/800',
                'desc' => 'El artista canario sorprende con un álbum conceptual que explora los sonidos tradicionales de las islas mezclados con ritmos urbanos.'
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

            if ($id === '7a8QhNYgKmcauIKB7rCyR5') { // El Baifo specific tracklist
                $tracksData = [
                    ['titulo' => 'Está en casa', 'duracion' => '3:15'],
                    ['titulo' => 'Caprichoso', 'duracion' => '2:40'],
                    ['titulo' => 'El Baifo', 'duracion' => '3:05'],
                    ['titulo' => 'Gáldar (ft. Tonny Tun Tun)', 'duracion' => '3:30'],
                    ['titulo' => 'Scandic', 'duracion' => '2:55'],
                    ['titulo' => 'Al golpito (ft. Nueva Línea)', 'duracion' => '3:20'],
                    ['titulo' => '2010ypico', 'duracion' => '2:50'],
                    ['titulo' => 'Algo va a pasar (ft. La Pantera, Lucho RK, Juseph)', 'duracion' => '4:10'],
                    ['titulo' => 'Hookah y calor', 'duracion' => '3:00'],
                    ['titulo' => 'Flakito', 'duracion' => '2:45'],
                    ['titulo' => 'Mi balcón', 'duracion' => '3:10'],
                    ['titulo' => 'La Graciosa (ft. Elvis Crespo)', 'duracion' => '3:45'],
                    ['titulo' => 'Ni borracho', 'duracion' => '2:50'],
                    ['titulo' => 'Hijo de volcán (ft. Los Gofiones)', 'duracion' => '4:00'],
                ];
                
                foreach ($tracksData as $index => $t) {
                    $album->canciones[] = (object)[
                        'id'      => 'baifo_track_' . $index,
                        'titulo'  => $t['titulo'],
                        'artista' => 'Quevedo',
                        'duracion'=> $t['duracion']
                    ];
                }
            }

            return view('album', compact('album', 'likedAlbums', 'likedSongs'));
        }

        if (isset($albumData['error'])) {
            abort(404, 'Álbum no encontrado o límite de peticiones.');
        }

        abort(404, 'Álbum no encontrado');
    }

    /**
     * Dynamically fetches album cover + tracklist from iTunes by artist + album name.
     * This is the route used by editorial news articles so they always work,
     * regardless of Spotify rate limits, and for any future article added.
     */
    public function showByItunes(string $artist, string $album)
    {
        $likedAlbums = [];
        $likedSongs = [];
        if (\Illuminate\Support\Facades\Auth::check()) {
            $likes = \App\Models\UserLike::where('user_id', \Illuminate\Support\Facades\Auth::id())->get();
            $likedAlbums = $likes->where('type', 'album')->pluck('spotify_id')->toArray();
            $likedSongs = $likes->where('type', 'song')->pluck('spotify_id')->toArray();
        }

        $artistDecoded = urldecode($artist);
        $albumDecoded  = urldecode($album);

        $albumData = $this->itunes->getAlbumBySearch($artistDecoded, $albumDecoded);

        if ($albumData) {
            return view('album', ['album' => $albumData, 'likedAlbums' => $likedAlbums, 'likedSongs' => $likedSongs]);
        }

        // Last-resort: show a minimal placeholder so the user is never left with a 404
        $fallbackAlbum = (object)[
            'id'          => 'itunes_notfound',
            'nombre'      => $albumDecoded,
            'portada_url' => asset('multimedia/img/default-album.jpg'),
            'artista'     => $artistDecoded,
            'artista_id'  => null,
            'descripcion' => 'No se encontró información del álbum en iTunes en este momento.',
            'canciones'   => [],
        ];

        return view('album', ['album' => $fallbackAlbum, 'likedAlbums' => $likedAlbums, 'likedSongs' => $likedSongs]);
    }
}
