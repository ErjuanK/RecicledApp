<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BusquedaController extends Controller
{
    private $spotify;
    
    public function __construct()
    {
        $clientId = config('services.spotify.client_id');
        $clientSecret = config('services.spotify.client_secret');
        $this->spotify = new \App\Services\SpotifyService($clientId, $clientSecret);
    }
    
    public function buscar(Request $request)
    {
        $termino = $request->input('termino', '');
        \Illuminate\Support\Facades\Log::info("Busqueda iniciada: " . $termino);
        
        // Validar que el término no esté vacío
        if (empty(trim($termino))) {
            return response()->json([]);
        }
        
        $resultados = [];
        
        try {
            // 1. BUSQUEDA LOCAL (Base de Datos)
            
            // Buscar Artistas Locales
            $artistasLocales = \App\Models\Artista::where('nombre_artistico', 'LIKE', "%{$termino}%")
                ->take(3)
                ->get();

            foreach ($artistasLocales as $artista) {
                $resultados[] = [
                    'nombre' => $artista->nombre_artistico,
                    'tipo' => 'artista',
                    'url' => route('artista.show', $artista->artista_id), // ID Numérico
                    'imagen' => $artista->foto_url ? asset($artista->foto_url) : asset('multimedia/img/default-artist.jpg')
                ];
            }

            // Buscar Canciones Locales
            $cancionesLocales = \App\Models\Cancion::where('titulo', 'LIKE', "%{$termino}%")
                ->with(['album.artista'])
                ->take(3)
                ->get();

            foreach ($cancionesLocales as $cancion) {
                $resultados[] = [
                    'nombre' => $cancion->titulo,
                    'artista' => $cancion->album->artista->nombre_artistico ?? 'Desconocido',
                    'tipo' => 'cancion',
                    'url' => route('cancion.show', $cancion->cancion_id),
                    'imagen' => $cancion->album->portada_url ? asset($cancion->album->portada_url) : asset('multimedia/img/default-song.jpg')
                ];
            }

            // 2. BUSQUEDA SPOTIFY (Fallback/Complemento)
            
            // Buscar artistas en Spotify
            $artistasSpotify = $this->spotify->searchArtist($termino);
            
            if ($artistasSpotify && !isset($artistasSpotify['error'])) {
                // Solo añadir si no hay ya suficientes resultados locales de artistas
                // O simplemente añadir como opción extra
                $resultados[] = [
                    'nombre' => $artistasSpotify['name'],
                    'tipo' => 'artista',
                    'url' => route('artista.show', $artistasSpotify['id']), // ID Spotify
                    'imagen' => $artistasSpotify['images'][0]['url'] ?? asset('multimedia/img/default-artist.jpg')
                ];
            }
            
            // Buscar canciones en Spotify
            $token = $this->spotify->getAccessToken();
            if ($token) {
                $urlCanciones = "https://api.spotify.com/v1/search?q=" . urlencode($termino) . "&type=track&limit=2";
                $ch = curl_init($urlCanciones);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $token,
                        'Content-Type: application/json'
                    ],
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_TIMEOUT => 5
                ]);
                
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($http_code === 200) {
                    $cancionesData = json_decode($response, true);
                    if (isset($cancionesData['tracks']['items'])) {
                        foreach ($cancionesData['tracks']['items'] as $track) {
                            $resultados[] = [
                                'nombre' => $track['name'],
                                'artista' => $track['artists'][0]['name'] ?? '',
                                'tipo' => 'cancion',
                                'url' => route('cancion.show', $track['id']), // Placeholder route logic handling spotify ID? Or assume it passes to a controller that handles it.
                                // Actually, route('cancion.show') isn't standard yet. Let's use # for now or fix route. 
                                // Re-reading web.php: Route::get('/cancion/{id}', ...)->name('cancion.show'); exists as placeholder.
                                // Assuming AlbumController/CancionController can handle Spotify IDs or Local IDs. 
                                // Since I haven't implemented CancionController@show for public yet, maybe link to Album? 
                                // Spotify Tracks link to Album usually in this app logic.
                                'url' => isset($track['album']['id']) ? route('album.show', $track['album']['id']) : '#',
                                'imagen' => $track['album']['images'][0]['url'] ?? asset('multimedia/img/default-song.jpg')
                            ];
                        }
                    }
                }
            }
            
            // Limitar a 8 resultados totales para no saturar
            $resultados = array_slice($resultados, 0, 8);
            
        } catch (\Exception $e) {
            \Log::error("Error en búsqueda: " . $e->getMessage());
            // No fallar, devolver lo que tengamos
        }
        
        return response()->json($resultados);
    }
}
