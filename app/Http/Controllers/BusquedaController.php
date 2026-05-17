<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\ItunesService;

class BusquedaController extends Controller
{
    private $itunes;
    
    public function __construct(ItunesService $itunes)
    {
        $this->itunes = $itunes;
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

            // 2. BUSQUEDA ITUNES (Fallback/Complemento)
            
            // Buscar artistas en iTunes
            $artistasItunes = $this->itunes->searchArtist($termino);
            
            if ($artistasItunes && !isset($artistasItunes['error'])) {
                // Solo añadir si no hay ya suficientes resultados locales de artistas
                $resultados[] = [
                    'nombre' => $artistasItunes['name'],
                    'tipo' => 'artista',
                    'url' => route('artista.show', $artistasItunes['id']),
                    'imagen' => $artistasItunes['images'][0]['url'] ?? asset('multimedia/img/default-artist.jpg')
                ];
            }
            
            // Buscar canciones en iTunes
            $cancionesItunes = $this->itunes->searchTracks($termino, 2);
            
            foreach ($cancionesItunes as $track) {
                $resultados[] = [
                    'nombre' => $track['name'],
                    'artista' => $track['artists'][0]['name'] ?? '',
                    'tipo' => 'cancion',
                    'url' => isset($track['album']['id']) ? route('album.show', $track['album']['id']) : '#',
                    'imagen' => $track['album']['images'][0]['url'] ?? asset('multimedia/img/default-song.jpg')
                ];
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
