<?php

namespace App\Http\Controllers;

use App\Models\Artista;
use App\Models\Album;
use App\Models\Cancion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        $usuarios = User::orderBy('id', 'desc')
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
        
        // Eliminar relaciones para evitar errores de restricción de clave foránea
        $usuario->artistas()->detach();
        DB::table('user_likes')->where('user_id', $id)->delete();
        if (Schema::hasTable('user_song_actions')) {
            DB::table('user_song_actions')->where('user_id', $id)->delete();
        }
        
        $usuario->delete();

        return redirect()->route('admin.usuarios')->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Delete an artist.
     */
    public function destroyArtista($id)
    {
        $artista = Artista::findOrFail($id);
        $artista->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Artista eliminado correctamente.');
    }

    public function editUsuario($id)
    {
        $usuario = User::findOrFail($id);
        return view('admin.usuarios_edit', compact('usuario'));
    }

    public function updateUsuario(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        
        $request->validate([
            'nombre_usuario' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$usuario->id,
            'rol' => 'required|in:admin,usuario,artista',
            'nombre_real' => 'nullable|string|max:255',
            'apellidos' => 'nullable|string|max:255',
        ]);

        // Using name or nombre_usuario depending on DB. The model uses both.
        if (Schema::hasColumn('users', 'nombre_usuario')) {
            $usuario->nombre_usuario = $request->nombre_usuario;
        } else {
            $usuario->name = $request->nombre_usuario;
        }
        $usuario->email = $request->email;
        $usuario->rol = $request->rol;
        $usuario->nombre_real = $request->nombre_real;
        $usuario->apellidos = $request->apellidos;
        $usuario->save();

        return redirect()->route('admin.usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    public function editAlbum($id)
    {
        $album = Album::with('artista')->findOrFail($id);
        return view('admin.albums_edit', compact('album'));
    }

    public function updateAlbum(Request $request, $id)
    {
        $album = Album::findOrFail($id);
        
        $request->validate([
            'titulo' => 'required|string|max:255',
            'estado' => 'required|in:publicado,borrador',
        ]);

        $album->titulo = $request->titulo;
        $album->estado = $request->estado;
        $album->save();

        return redirect()->route('admin.albums')->with('success', 'Álbum actualizado correctamente.');
    }

    public function editCancion($id)
    {
        $cancion = Cancion::with('artista', 'album')->findOrFail($id);
        return view('admin.canciones_edit', compact('cancion'));
    }

    public function updateCancion(Request $request, $id)
    {
        $cancion = Cancion::findOrFail($id);
        
        $request->validate([
            'titulo' => 'required|string|max:255',
            'estado' => 'required|in:publico,privado,oculto',
        ]);

        $cancion->titulo = $request->titulo;
        $cancion->estado = $request->estado;
        $cancion->save();

        return redirect()->route('admin.canciones')->with('success', 'Canción actualizada correctamente.');
    }
}
