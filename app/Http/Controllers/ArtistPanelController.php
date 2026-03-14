<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Artista;
use App\Models\Cancion;
use App\Models\User;

class ArtistPanelController extends Controller
{
    /**
     * Show the artist control panel.
     */
    /**
     * Show the list of artists managed by the user.
     */
    public function index()
    {
        $user = Auth::user();
        $artistas = $user->artistas;

        // Redirect if only one artist
        if ($artistas->count() === 1) {
            return redirect()->route('artist.panel.dashboard', $artistas->first()->artista_id);
        }

        return view('artista.panel.selection_screen', compact('artistas'));
    }

    /**
     * Show the dashboard for a specific artist.
     */
    public function manage($id)
    {
        $user = Auth::user();
        
        // Ensure user is an editor of this artist
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();

        // Load albums and songs
        $albumes = $artista->albums()->with('canciones')->get();
        
        // Load ALL songs for this artist using Eloquent Relationships
        $canciones = $artista->canciones()
            ->with('album')
            ->get()
            ->map(function ($cancion) {
                $cancion->nombre_album = $cancion->album ? $cancion->album->titulo : 'Sin álbum';
                $cancion->portada_album = $cancion->album ? $cancion->album->portada_url : null;
                return $cancion;
            });

        return view('artista.panel.index', compact('user', 'artista', 'albumes', 'canciones'));
    }

    /**
     * Update the artist profile.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();

        $request->validate([
            'nombre_artistico' => 'required|string|max:255|unique:artista,nombre_artistico,' . $artista->artista_id . ',artista_id',
            'biografia' => 'nullable|string',
            
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nombre_real' => 'nullable|string|max:100',
            'apellidos' => 'nullable|string|max:100',
            'pais' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
            'codigo_postal' => 'nullable|string|max:20',
            'password' => 'nullable|confirmed|min:4',
            
            'avatar' => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // 1. Update User Data
            $userData = $request->only(['email', 'nombre_real', 'apellidos', 'pais', 'ciudad', 'codigo_postal']);
            
            if ($request->filled('password')) {
                $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            }

            if ($request->hasFile('avatar')) {
                $filename = 'artist_' . $artista->artista_id . '_' . time() . '.' . $request->avatar->getClientOriginalExtension();
                $request->avatar->move(public_path('multimedia/img/avatars'), $filename);
                $userData['avatar'] = 'multimedia/img/avatars/' . $filename;
            }

            User::where('id', $user->id)->update($userData);

            // 2. Update Artist Data
            $artista->nombre_artistico = $request->nombre_artistico;
            $artista->biografia = $request->biografia;
            
            if (isset($userData['avatar'])) {
                $artista->foto_url = $userData['avatar'];
            }
            
            $artista->save();

            DB::commit();

            return back()->with('success', 'Perfil de artista actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])->withInput();
        }
    }
}
