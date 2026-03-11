<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Album;
use App\Models\Cancion;

class ArtistSongController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    /**
     * Show the form for creating a new resource.
     */
    public function create($id, $albumId)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();
        
        $album = Album::where('album_id', $albumId)->where('artista_id', $artista->artista_id)->firstOrFail();
        
        return view('artista.panel.cancion.create', compact('artista', 'album'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id, $albumId)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();
        $album = Album::where('album_id', $albumId)->where('artista_id', $artista->artista_id)->firstOrFail();

        $request->validate([
            'titulo' => 'required|string|max:255',
            'duracion_min' => 'required|integer|min:0',
            'duracion_sec' => 'required|integer|min:0|max:59',
            'contexto' => 'nullable|string',
            'estado' => 'required|in:publico,privado,oculto',
            'letra' => 'nullable|string'
        ]);

        $totalSeconds = ($request->duracion_min * 60) + $request->duracion_sec;

        $cancion = new Cancion();
        $cancion->album_id = $album->album_id;
        $cancion->artista_id = $artista->artista_id;
        $cancion->titulo = $request->titulo;
        $cancion->duracion = $totalSeconds;
        $cancion->contexto = $request->contexto;
        // Handling Credits simply as text for now, could be JSON later
        $cancion->creditos = $request->creditos; 
        $cancion->estado = $request->estado;
        
        $cancion->save();

        if (!empty($request->letra)) {
            $letra = new \App\Models\Letra();
            $letra->cancion_id = $cancion->cancion_id;
            $letra->contenido = $request->letra;
            $letra->save();
        }

        return redirect()->route('artist.panel.dashboard', $artista->artista_id)->with('success', 'Canción añadida al álbum.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, $albumId, $cancionId)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();
        $album = Album::where('album_id', $albumId)->where('artista_id', $artista->artista_id)->firstOrFail();
        $cancion = Cancion::where('cancion_id', $cancionId)->where('album_id', $album->album_id)->firstOrFail();

        return view('artista.panel.cancion.edit', compact('artista', 'album', 'cancion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id, $albumId, $cancionId)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();
        $album = Album::where('album_id', $albumId)->where('artista_id', $artista->artista_id)->firstOrFail();
        $cancion = Cancion::where('cancion_id', $cancionId)->where('album_id', $album->album_id)->firstOrFail();

        $request->validate([
            'titulo' => 'required|string|max:255',
            'duracion_min' => 'required|integer|min:0',
            'duracion_sec' => 'required|integer|min:0|max:59',
            'contexto' => 'nullable|string',
            'estado' => 'required|in:publico,privado,oculto'
        ]);

        $totalSeconds = ($request->duracion_min * 60) + $request->duracion_sec;

        $cancion->titulo = $request->titulo;
        $cancion->duracion = $totalSeconds;
        $cancion->contexto = $request->contexto;
        $cancion->creditos = $request->creditos;
        $cancion->estado = $request->estado;
        
        $cancion->save();

        return redirect()->route('artist.panel.album.index', $artista->artista_id)->with('success', 'Canción actualizada.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $albumId, $cancionId)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();
        $album = Album::where('album_id', $albumId)->where('artista_id', $artista->artista_id)->firstOrFail();
        $cancion = Cancion::where('cancion_id', $cancionId)->where('album_id', $album->album_id)->firstOrFail();

        $cancion->delete();

        return back()->with('success', 'Canción eliminada.');
    }
    /**
     * Show the form for creating a new resource (Standalone).
     */
    public function createStandalone($id)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();
        $albumes = $artista->albums; 
        
        return view('artista.panel.cancion.create', compact('artista', 'albumes'));
    }

    /**
     * Store a newly created resource in storage (Standalone).
     */
    public function storeStandalone(Request $request, $id)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();

        $request->validate([
            'titulo' => 'required|string|max:255',
            'duracion_min' => 'required|integer|min:0',
            'duracion_sec' => 'required|integer|min:0|max:59',
            'contexto' => 'nullable|string',
            'estado' => 'required|in:publico,privado,oculto',
            'letra' => 'nullable|string',
            'album_id' => 'nullable|exists:album,album_id', // Optional album selection
            'portada' => 'nullable|image|max:2048'
        ]);

        // If album_id is provided, verify it belongs to artist
        if ($request->filled('album_id')) {
            $album = Album::where('album_id', $request->album_id)
                          ->where('artista_id', $artista->artista_id)
                          ->firstOrFail();
        }

        $totalSeconds = ($request->duracion_min * 60) + $request->duracion_sec;

        $cancion = new Cancion();
        $cancion->album_id = $request->album_id ?? null;
        $cancion->artista_id = $artista->artista_id;
        $cancion->titulo = $request->titulo;
        $cancion->duracion = $totalSeconds;
        $cancion->contexto = $request->contexto;
        $cancion->creditos = $request->creditos; 
        $cancion->estado = $request->estado;
        
        if ($request->hasFile('portada')) {
            $filename = 'song_' . $artista->artista_id . '_' . time() . '.' . $request->portada->getClientOriginalExtension();
            $request->portada->move(public_path('multimedia/img/Portadas/canciones'), $filename);
            $cancion->portada = 'multimedia/img/Portadas/canciones/' . $filename;
        } elseif ($request->filled('album_id') && isset($album)) {
            // Inherit album cover if song has no specific cover but belongs to an album
            $cancion->portada = $album->portada_url;
        }

        $cancion->save();

        if (!empty($request->letra)) {
            $letra = new \App\Models\Letra();
            $letra->cancion_id = $cancion->cancion_id;
            $letra->contenido = $request->letra;
            $letra->save();
        }

        return redirect()->route('artist.panel.dashboard', $artista->artista_id)->with('success', 'Canción creada exitosamente.');
    }
}
