<?php

namespace App\Http\Controllers;

use App\Models\Artista;
use App\Models\Album;
use App\Models\Cancion;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'artistas' => Artista::count(),
            'albumes' => Album::count(),
            'canciones' => Cancion::count(),
            'usuarios' => User::count(),
        ];

        $artistas = Artista::withCount(['albums', 'canciones'])
            ->orderBy('artista_id', 'desc')
            ->paginate(10);

        return view('admin.dashboard', compact('stats', 'artistas'));
    }

    /**
     * Display all albums.
     */
    public function albums()
    {
        $albums = Album::with('artista')
            ->withCount('canciones')
            ->orderBy('album_id', 'desc')
            ->paginate(10);

        return view('admin.albums', compact('albums'));
    }

    /**
     * Display all songs.
     */
    public function canciones()
    {
        $canciones = Cancion::with(['artista', 'album'])
            ->orderBy('cancion_id', 'desc')
            ->paginate(10);

        return view('admin.canciones', compact('canciones'));
    }

    /**
     * Display all users.
     */
    public function usuarios()
    {
        $usuarios = User::orderBy('usuario_id', 'desc')
            ->paginate(10);

        return view('admin.usuarios', compact('usuarios'));
    }

    /**
     * Delete an album.
     */
    public function destroyAlbum($id)
    {
        $album = Album::findOrFail($id);
        $album->delete();

        return redirect()->route('admin.albums')->with('success', 'Álbum eliminado correctamente.');
    }

    /**
     * Delete a song.
     */
    public function destroyCancion($id)
    {
        $cancion = Cancion::findOrFail($id);
        $cancion->delete();

        return redirect()->route('admin.canciones')->with('success', 'Canción eliminada correctamente.');
    }

    /**
     * Delete a user.
     */
    public function destroyUsuario($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();

        return redirect()->route('admin.usuarios')->with('success', 'Usuario eliminado correctamente.');
    }
}
