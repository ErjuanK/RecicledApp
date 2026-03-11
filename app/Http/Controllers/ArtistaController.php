<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArtistaController extends Controller
{
    private $spotify;
    private $genius;
    
    public function __construct()
    {
        // Cargar servicios sin namespace completo (se ajustará después)
        $clientId = 'cf0a28b6c1c9425bbfb697f9a072afc8';
        $clientSecret = '16c9bcf6476e47138c1adc87c82596ea';
        $this->spotify = new \App\Services\SpotifyService($clientId, $clientSecret);
        $this->genius = new \App\Services\GeniusService();
    }
    
    public function show($id)
    {
        // 1. Try Local DB lookup (if numeric ID)
        if (is_numeric($id)) {
            $localArtist = \App\Models\Artista::with('albums.canciones', 'generos')->find($id);
            
            if ($localArtist) {
                // Map Local Data to View Object
                $artista = (object)[
                    'id' => $localArtist->artista_id,
                    'nombre_artistico' => $localArtist->nombre_artistico,
                    'imagen_hero' => $localArtist->foto_url ? asset($localArtist->foto_url) : asset('multimedia/img/default-bg.jpg'),
                    'imagen_perfil' => $localArtist->foto_url ? asset($localArtist->foto_url) : asset('multimedia/img/default-artist.jpg'),
                    'seguidores' => 'N/A', // O implementar contador local
                    'biografia' => $localArtist->biografia ?: "Biografía no disponible.",
                    'generos' => $localArtist->generos->pluck('nombre')->toArray(),
                    'albumes' => [],
                    'canciones_populares' => [],
                    'todas_las_canciones' => [],
                    'aka' => $localArtist->nombre_artistico,
                    'contributors' => 1,
                    'is_verified' => true
                ];

                // Process Albums
                foreach ($localArtist->albums as $album) {
                    // Logic to show only public arrays if needed, assuming all are visible for now or filtering in query
                    $artista->albumes[] = (object)[
                        'id' => $album->album_id,
                        'nombre_album' => $album->titulo,
                        'portada_url' => $album->portada_url ? asset($album->portada_url) : asset('multimedia/img/default-album.jpg'),
                        'anio' => $album->fecha_lanzamiento ? substr($album->fecha_lanzamiento, 0, 4) : 'Unknown'
                    ];

                    // Process Songs for "All Songs" list
                    foreach ($album->canciones as $cancion) {
                        $min = floor($cancion->duracion / 60);
                        $sec = $cancion->duracion % 60;
                        
                        $songObj = (object)[
                            'id' => $cancion->cancion_id,
                            'titulo' => $cancion->titulo,
                            'miniatura_url' => $album->portada_url ? asset($album->portada_url) : asset('multimedia/img/default-song.jpg'),
                            'artista_nombre' => $localArtist->nombre_artistico,
                            'album_nombre' => $album->titulo,
                            'vistas_formateadas' => '-',
                            'duracion_formateada' => sprintf("%d:%02d", $min, $sec)
                        ];

                        $artista->todas_las_canciones[] = $songObj;
                        
                        // Populate "Popular" with first few songs for now
                        if (count($artista->canciones_populares) < 5) {
                            $artista->canciones_populares[] = $songObj;
                        }
                    }
                }

                return view('artista', compact('artista'));
            }
        }

        // 2. Fallback to Spotify Logic
        $artistData = null;
        
        // Determinar si es ID o búsqueda por texto
        if (preg_match('/^[a-zA-Z0-9]{22}$/', $id)) {
            $artistData = $this->spotify->getArtist($id);
        }
        
        // Fallback or search if not found/valid ID
        if (!$artistData) {
            $artistData = $this->spotify->searchArtist($id);
            if ($artistData) $id = $artistData['id'];
        }
        
        if ($artistData) {
            $topTracks = $this->spotify->getArtistTopTracks($artistData['id']);
            $albums = $this->spotify->getArtistAlbums($artistData['id'], 8);
            
            // Construir objeto de vista optimizado
            $artista = (object)[
                'id' => $artistData['id'],
                'nombre_artistico' => $artistData['name'],
                'imagen_hero' => $artistData['images'][0]['url'] ?? asset('multimedia/img/default-bg.jpg'),
                'imagen_perfil' => $artistData['images'][1]['url'] ?? ($artistData['images'][0]['url'] ?? asset('multimedia/img/default-artist.jpg')),
                'seguidores' => number_format($artistData['followers']['total']),
                'biografia' => $this->generarBiografia($artistData),
                'generos' => $artistData['genres'] ?? [],
                'albumes' => [],
                'canciones_populares' => [],
                'aka' => 'Artist',
                'contributors' => rand(2, 15),
                'is_verified' => true
            ];
            
            // Procesar Álbumes
            foreach ($albums as $album) {
                $artista->albumes[] = (object)[
                    'id' => $album['id'],
                    'nombre_album' => $album['name'],
                    'portada_url' => $album['images'][0]['url'] ?? asset('multimedia/img/default-album.jpg'),
                    'anio' => substr($album['release_date'], 0, 4)
                ];
            }
            
            // Procesar Canciones
            foreach ($topTracks as $track) {
                $minutes = floor($track['duration_ms'] / 60000);
                $seconds = ($track['duration_ms'] % 60000) / 1000;
                
                $artista->canciones_populares[] = (object)[
                    'id' => $track['id'],
                    'titulo' => $track['name'],
                    'miniatura_url' => $track['album']['images'][0]['url'] ?? asset('multimedia/img/default-song.jpg'),
                    'artista_nombre' => $track['artists'][0]['name'],
                    'album_nombre' => $track['album']['name'] ?? '',
                    'vistas_formateadas' => $this->formatearVistas($track['popularity']),
                    'duracion_formateada' => sprintf("%d:%02d", $minutes, $seconds)
                ];
            }

            // Obtener TODAS las canciones (de los álbumes)
            $todasLasCanciones = [];
            $trackIds = [];

            // Agregar primero las populares
            foreach ($artista->canciones_populares as $popSong) {
                $todasLasCanciones[] = $popSong;
                $trackIds[$popSong->id] = true;
            }

            // Recorrer los primeros 3 álbumes para obtener más canciones (Optimización: Reducido de 10 a 3)
            $albumsLimitados = array_slice($albums, 0, 3);
            
            foreach ($albumsLimitados as $album) {
                $albumTracks = $this->spotify->getAlbumTracks($album['id']);
                
                foreach ($albumTracks as $track) {
                    if (!isset($trackIds[$track['id']])) {
                        $todasLasCanciones[] = (object)[
                            'id' => $track['id'],
                            'titulo' => $track['name'],
                            'miniatura_url' => $album['images'][2]['url'] ?? ($album['images'][0]['url'] ?? asset('multimedia/img/default-song.jpg')),
                            'artista_nombre' => $track['artists'][0]['name'],
                            'album_nombre' => $album['name'],
                            'vistas_formateadas' => '',
                            'duracion_formateada' => ''
                        ];
                        $trackIds[$track['id']] = true;
                    }
                }
            }
            
            $artista->todas_las_canciones = $todasLasCanciones;
            
            return view('artista', compact('artista'));
            
        } else {
            abort(404, 'Artista no encontrado');
        }
    }
    
    private function generarBiografia($data)
    {
        $nombreArtista = $data['name'];
        $geniusId = $this->genius->obtenerIdArtista($nombreArtista);
        
        if ($geniusId) {
            $bio = $this->genius->obtenerBiografia($geniusId);
            if ($bio) {
                return $bio;
            }
        }
        
        // Fallback
        return "Perfil oficial de {$data['name']} en nuestra plataforma. " . 
               "Con {$data['followers']['total']} seguidores en Spotify, " . 
               "se posiciona como uno de los artistas destacados del género " . 
               (isset($data['genres'][0]) ? ucwords($data['genres'][0]) : 'Musical') . ".";
    }
    
    private function formatearVistas($popularity)
    {
        $base = $popularity * 10000;
        if ($base > 1000000) {
            return round($base / 1000000, 1) . 'M';
        } elseif ($base > 1000) {
            return round($base / 1000, 1) . 'K';
        }
        return $base;
    }
}
