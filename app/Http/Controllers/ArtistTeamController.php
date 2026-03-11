<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Artista;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ArtistTeamController extends Controller
{
    /**
     * Show list of editors for this artist.
     */
    public function index($id)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();
        
        // Fetch all editors including the current user
        $editors = $artista->editores; 

        return view('artista.panel.team.index', compact('artista', 'editors'));
    }

    /**
     * Add a new editor by email.
     */
    public function store(Request $request, $id)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();

        $request->validate([
            'email' => 'required|email|exists:usuario,email'
        ]);

        $usuarioInvitado = User::where('email', $request->email)->first();

        // Check if already an editor
        if ($artista->editores()->where('usuario.usuario_id', $usuarioInvitado->usuario_id)->exists()) {
            return back()->with('error', 'El usuario ya es editor de este artista.');
        }

        // Check if self
        if ($usuarioInvitado->usuario_id === $user->usuario_id) {
             return back()->with('error', 'Ya eres editor de este artista (obviamente).');
        }

        // Add relationship
        // Assuming pivot table 'artista_editor'
        $artista->editores()->attach($usuarioInvitado->usuario_id, ['rol' => 'editor', 'fecha_asignacion' => now()]);

        return back()->with('success', 'Editor añadido correctamente.');
    }

    /**
     * Remove an editor.
     */
    public function destroy($id, $userId)
    {
        $user = Auth::user();
        $artista = $user->artistas()->where('artista.artista_id', $id)->firstOrFail();

        if ($userId == $user->usuario_id) {
            return back()->with('error', 'No puedes eliminarte a ti mismo desde aquí. Contacta a soporte o elimina tu cuenta.');
        }

        $artista->editores()->detach($userId);

        return back()->with('success', 'Editor eliminado.');
    }
}
