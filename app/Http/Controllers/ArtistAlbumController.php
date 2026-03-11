<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Album;
use App\Models\Artista;

class ArtistAlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();
        $albumes = $artista->albums()->orderBy('fecha_lanzamiento', 'desc')->get();

        return view('artista.panel.album.index', compact('artista', 'albumes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();
        
        return view('artista.panel.album.create', compact('artista'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();
        
        $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_lanzamiento' => 'nullable|date',
            'contexto' => 'nullable|string',
            'estado' => 'required|in:publico,privado,oculto',
            'portada' => 'nullable|image|max:2048',
            'canciones' => 'required|array|min:1',
            'canciones.*.titulo' => 'required|string|max:255',
            'canciones.*.duracion_min' => 'required|integer|min:0',
            'canciones.*.duracion_sec' => 'required|integer|min:0|max:59',
            'canciones.*.estado' => 'required|in:publico,privado,oculto',
            'canciones.*.creditos' => 'nullable|string',
            'canciones.*.letra' => 'nullable|string',
            // 'canciones.*.audio' => 'nullable|file|mimes:mp3,wav' // Uncomment when ready to handle audio files
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. Create Album
            $album = new Album();
            $album->artista_id = $artista->artista_id;
            $album->titulo = $request->titulo;
            $album->fecha_lanzamiento = $request->fecha_lanzamiento;
            $album->contexto = $request->contexto;
            $album->estado = $request->estado;

            if ($request->hasFile('portada')) {
                $filename = 'album_' . $artista->artista_id . '_' . time() . '.' . $request->portada->getClientOriginalExtension();
                $request->portada->move(public_path('multimedia/img/Portadas/album'), $filename);
                $album->portada_url = 'multimedia/img/Portadas/album/' . $filename;
            }

            $album->save();

            // 2. Create Songs
            foreach ($request->canciones as $index => $songData) {
                $totalSeconds = ($songData['duracion_min'] * 60) + $songData['duracion_sec'];

                $cancion = new \App\Models\Cancion();
                $cancion->album_id = $album->album_id;
                $cancion->titulo = $songData['titulo'];
                $cancion->duracion = $totalSeconds;
                // $cancion->contexto = ...; // Not in form yet
                $cancion->creditos = $songData['creditos'] ?? null;
                $cancion->estado = $songData['estado'];
                $cancion->save();

                // 3. Create Lyrics (if provided)
                if (!empty($songData['letra'])) {
                    $letra = new \App\Models\Letra();
                    $letra->cancion_id = $cancion->cancion_id;
                    $letra->contenido = $songData['letra'];
                    $letra->save();
                }

                // 4. Handle Audio File (Logic placeholder)
                // if ($request->hasFile("canciones.$index.audio")) { ... }
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('artist.panel.dashboard', $artista->artista_id)->with('success', 'Álbum y canciones creados exitosamente.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el álbum: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, $albumId)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();
        $album = Album::where('album_id', $albumId)->where('artista_id', $artista->artista_id)->firstOrFail();

        return view('artista.panel.album.edit', compact('artista', 'album'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id, $albumId)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();
        $album = Album::where('album_id', $albumId)->where('artista_id', $artista->artista_id)->firstOrFail();

        $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_lanzamiento' => 'nullable|date',
            'contexto' => 'nullable|string',
            'estado' => 'required|in:publico,privado,oculto',
            'portada' => 'nullable|image|max:2048'
        ]);

        $album->titulo = $request->titulo;
        $album->fecha_lanzamiento = $request->fecha_lanzamiento;
        $album->contexto = $request->contexto;
        $album->estado = $request->estado;

        if ($request->hasFile('portada')) {
            $filename = 'album_' . $artista->artista_id . '_' . time() . '.' . $request->portada->getClientOriginalExtension();
            $request->portada->move(public_path('multimedia/img/Portadas/album'), $filename);
            $album->portada_url = 'multimedia/img/Portadas/album/' . $filename;
        }

        $album->save();

        return redirect()->route('artist.panel.album.index', $artista->artista_id)->with('success', 'Álbum actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $albumId)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();
        $album = Album::where('album_id', $albumId)->where('artista_id', $artista->artista_id)->firstOrFail();

        $album->delete();

        return redirect()->route('artist.panel.album.index', $artista->artista_id)->with('success', 'Álbum eliminado.');
    }
}
