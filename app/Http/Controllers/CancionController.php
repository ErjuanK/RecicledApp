<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\ItunesService;

class CancionController extends Controller
{
    private $itunes;
    private $genius;
    
    public function __construct(ItunesService $itunes)
    {
        $this->itunes = $itunes;
        $this->genius = new \App\Services\GeniusService();
    }
    
    public function show($id)
    {
        $likedSongs = [];
        if (\Illuminate\Support\Facades\Auth::check()) {
            $likedSongs = \App\Models\UserLike::where('user_id', \Illuminate\Support\Facades\Auth::id())
                                              ->where('type', 'song')
                                              ->pluck('spotify_id')
                                              ->toArray();
        }

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
                    $titCancion  = $localCancion->titulo;
                    $nomArtista  = $localCancion->album->artista->nombre_artistico;
                    $query = $titCancion . ' ' . $nomArtista;
                    $geniusData = $this->genius->buscarCancion($query);

                    if ($geniusData && isset($geniusData['url'])) {
                        $geniusUrl = $geniusData['url'];
                        $letraHtml = $this->genius->obtenerLetra($geniusUrl);
                    }

                    // Si Genius fue bloqueado (producción), intentar APIs alternativas
                    if (!$letraHtml) {
                        $letraHtml = $this->genius->obtenerLetraFallback($nomArtista, $titCancion);
                    }

                    if ($letraHtml) {
                        $newLetra = new \App\Models\Letra();
                        $newLetra->cancion_id = $localCancion->cancion_id;
                        $newLetra->contenido = $letraHtml;
                        $newLetra->save();
                        $localLetraId = $newLetra->letra_id;
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

                return view('cancion', compact('cancion', 'likedSongs'));
            }
        }

        // 2. Fallback to iTunes Logic
        $trackData = $this->itunes->getTrack($id);

        if ($trackData && !isset($trackData['error'])) {
            $minutes = floor($trackData['duration_ms'] / 60000);
            $seconds = ($trackData['duration_ms'] % 60000) / 1000;

            // Check if we have the song locally to get internal ID
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
                    $titTrack    = $trackData['name'];
                    $nomArtTrack = $trackData['artists'][0]['name'];
                    $query = $titTrack . ' ' . $nomArtTrack;
                    $geniusData = $this->genius->buscarCancion($query);

                    if ($geniusData && isset($geniusData['url'])) {
                        $geniusUrl = $geniusData['url'];
                        $letraHtml = $this->genius->obtenerLetra($geniusUrl);
                    }

                    if (!$letraHtml) {
                        $letraHtml = $this->genius->obtenerLetraFallback($nomArtTrack, $titTrack);
                    }

                    if ($letraHtml) {
                        $newLetra = new \App\Models\Letra();
                        $newLetra->cancion_id = $localCancion->cancion_id;
                        $newLetra->contenido  = $letraHtml;
                        $newLetra->save();
                        $localLetraId = $newLetra->letra_id;
                    }
                }
            } else {
                // Fallback for non-local songs (view only, no annotations)
                $titSong    = $trackData['name'];
                $nomArtSong = $trackData['artists'][0]['name'];
                $query = $titSong . ' ' . $nomArtSong;
                $geniusData = $this->genius->buscarCancion($query);

                if ($geniusData && isset($geniusData['url'])) {
                    $geniusUrl = $geniusData['url'];
                    $letraHtml = $this->genius->obtenerLetra($geniusUrl);
                }

                if (!$letraHtml) {
                    $letraHtml = $this->genius->obtenerLetraFallback($nomArtSong, $titSong);
                }
            }

            // Build View Object
            $cancion = (object)[
                'id'            => $trackData['id'],
                'titulo'        => $trackData['name'],
                'portada_url'   => $trackData['album']['images'][0]['url'] ?? asset('multimedia/img/default-song.jpg'),
                'artista'       => $trackData['artists'][0]['name'],
                'artista_id'    => $trackData['artists'][0]['id'],
                'album'         => $trackData['album']['name'],
                'album_id'      => $trackData['album']['id'],
                'duracion'      => sprintf("%d:%02d", $minutes, $seconds),
                'creditos'      => [
                    ['rol' => 'Escrito por', 'nombres' => $trackData['artists'][0]['name']]
                ],
                'letra_html'    => $letraHtml,
                'url_genius'    => $geniusUrl,
                'letra_id'      => $localLetraId,
                'anotaciones'   => $anotaciones,
                'letra_simulada' => [
                    "No se pudo cargar la letra automáticamente desde Genius.",
                    "Intenta visitar el enlace oficial: " . ($geniusUrl ?: "No disponible")
                ]
            ];

            return view('cancion', compact('cancion', 'likedSongs'));
        }

        // ── Canción no encontrada: página amigable en lugar de 404 feo ──
        return view('cancion_no_encontrada', ['songId' => $id])->setStatusCode(404);
    }
}
