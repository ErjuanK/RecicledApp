<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CancionController extends Controller
{
    private $spotify;
    private $genius;
    
    public function __construct()
    {
        $clientId = 'cf0a28b6c1c9425bbfb697f9a072afc8';
        $clientSecret = '16c9bcf6476e47138c1adc87c82596ea';
        $this->spotify = new \App\Services\SpotifyService($clientId, $clientSecret);
        $this->genius = new \App\Services\GeniusService();
    }
    
    public function show($id)
    {
        // 1. Try Local DB lookup (if numeric ID)
        if (is_numeric($id)) {
            $localCancion = \App\Models\Cancion::with('album.artista')->find($id);

            if ($localCancion) {
                // Initialize variables for lyrics/annotations
                $letraHtml = '';
                $geniusUrl = '';
                $localLetraId = null;
                $anotaciones = [];

                // Check for existing Letra
                $localLetra = \App\Models\Letra::where('cancion_id', $localCancion->cancion_id)->first();
                
                if ($localLetra) {
                    $letraHtml = $localLetra->contenido;
                    $localLetraId = $localLetra->letra_id;
                    $anotaciones = $localLetra->anotaciones()->where('estado', 'aprobada')->get();
                } else {
                    // Try to fetch from Genius based on Title + Artist
                    // This allows local songs to have lyrics automatically if they are famous covers or just indexed
                    $query = $localCancion->titulo . ' ' . $localCancion->album->artista->nombre_artistico;
                    $geniusData = $this->genius->buscarCancion($query);
                    
                    if ($geniusData && isset($geniusData['url'])) {
                        $geniusUrl = $geniusData['url'];
                        $letraHtml = $this->genius->obtenerLetra($geniusUrl);
                        
                        if ($letraHtml) {
                            $newLetra = new \App\Models\Letra();
                            $newLetra->cancion_id = $localCancion->cancion_id;
                            $newLetra->contenido = $letraHtml;
                            $newLetra->save();
                            $localLetraId = $newLetra->letra_id;
                        }
                    }
                }

                $min = floor($localCancion->duracion / 60);
                $sec = $localCancion->duracion % 60;

                // Build View Object from Local Data
                $cancion = (object)[
                    'id' => $localCancion->cancion_id,
                    'titulo' => $localCancion->titulo,
                    'portada_url' => $localCancion->album->portada_url ? asset($localCancion->album->portada_url) : asset('multimedia/img/default-song.jpg'),
                    'artista' => $localCancion->album->artista->nombre_artistico,
                    'artista_id' => $localCancion->album->artista_id,
                    'album' => $localCancion->album->titulo,
                    'album_id' => $localCancion->album_id,
                    'duracion' => sprintf("%d:%02d", $min, $sec),
                    'creditos' => [
                        ['rol' => 'Créditos', 'nombres' => $localCancion->creditos ?? $localCancion->album->artista->nombre_artistico]
                    ],
                    'letra_html' => $letraHtml,
                    'url_genius' => $geniusUrl,
                    'letra_id' => $localLetraId,
                    'anotaciones' => $anotaciones,
                    'letra_simulada' => [
                        "Letra no disponible.",
                        "Prueba añadirla desde el panel."
                    ]
                ];

                return view('cancion', compact('cancion'));
            }
        }

        // 2. Fallback to Spotify Logic
        $trackData = $this->spotify->getTrack($id);
        
        if ($trackData && !isset($trackData['error'])) {
            $minutes = floor($trackData['duration_ms'] / 60000);
            $seconds = ($trackData['duration_ms'] % 60000) / 1000;
            
            // Check if we have the song locally to get internal ID (Simplified logic from before)
            $localCancion = \App\Models\Cancion::where('titulo', $trackData['name'])
                            ->whereHas('album.artista', function($q) use ($trackData) {
                                $q->where('nombre_artistico', $trackData['artists'][0]['name']);
                            })->first();

            $letraHtml = '';
            $geniusUrl = '';
            $localLetraId = null;
            $anotaciones = [];

            if ($localCancion) {
                // Check for local lyrics
                $localLetra = \App\Models\Letra::where('cancion_id', $localCancion->cancion_id)->first();
                
                if ($localLetra) {
                    $letraHtml = $localLetra->contenido;
                    $localLetraId = $localLetra->letra_id;
                    $anotaciones = $localLetra->anotaciones()->where('estado', 'aprobada')->get();
                } else {
                    // Fetch from Genius and Save
                    $query = $trackData['name'] . ' ' . $trackData['artists'][0]['name'];
                    $geniusData = $this->genius->buscarCancion($query);
                    
                    if ($geniusData && isset($geniusData['url'])) {
                        $geniusUrl = $geniusData['url'];
                        $letraHtml = $this->genius->obtenerLetra($geniusUrl);
                        
                        if ($letraHtml) {
                            $newLetra = new \App\Models\Letra();
                            $newLetra->cancion_id = $localCancion->cancion_id;
                            $newLetra->contenido = $letraHtml;
                            $newLetra->save();
                            $localLetraId = $newLetra->letra_id;
                        }
                    }
                }
            } else {
                // Fallback for non-local songs (View only, no annotations)
                $query = $trackData['name'] . ' ' . $trackData['artists'][0]['name'];
                $geniusData = $this->genius->buscarCancion($query);
                if ($geniusData && isset($geniusData['url'])) {
                    $geniusUrl = $geniusData['url'];
                    $letraHtml = $this->genius->obtenerLetra($geniusUrl);
                }
            }

            // Build View Object
            $cancion = (object)[
                'id' => $trackData['id'], // Spotify ID
                'titulo' => $trackData['name'],
                'portada_url' => $trackData['album']['images'][0]['url'] ?? asset('multimedia/img/default-song.jpg'),
                'artista' => $trackData['artists'][0]['name'],
                'artista_id' => $trackData['artists'][0]['id'],
                'album' => $trackData['album']['name'],
                'album_id' => $trackData['album']['id'],
                'duracion' => sprintf("%d:%02d", $minutes, $seconds),
                'creditos' => [
                    ['rol' => 'Written By', 'nombres' => $trackData['artists'][0]['name']],
                    ['rol' => 'Produced By', 'nombres' => 'Producer Name'],
                    ['rol' => 'Label', 'nombres' => 'Record Label']
                ],
                'letra_html' => $letraHtml,
                'url_genius' => $geniusUrl,
                'letra_id' => $localLetraId, // Important for AJAX annotations
                'anotaciones' => $anotaciones,
                'letra_simulada' => [
                    "No se pudo cargar la letra automáticamente desde Genius.",
                    "Intenta visitar el enlace oficial: " . ($geniusUrl ?: "No disponible")
                ]
            ];
            
            return view('cancion', compact('cancion'));
        } else {
            abort(404, 'Canción no encontrada');
        }
    }
}
