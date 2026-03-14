<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('usuario.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nombre_real' => 'nullable|string|max:100',
            'apellidos' => 'nullable|string|max:100',
            'calle' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:20',
            'ciudad' => 'nullable|string|max:100',
            'pais' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|max:2048', // 2MB Max
            'password' => 'nullable|confirmed|min:4',
        ]);

        try {
            $data = $request->only([
                'email', 'nombre_real', 'apellidos', 'calle', 'codigo_postal', 'ciudad', 'pais'
            ]);

            // Handle Avatar Upload
            if ($request->hasFile('avatar')) {
                // Generate filename like user_123_163123123.jpg
                $filename = 'user_' . $user->id . '_' . time() . '.' . $request->avatar->getClientOriginalExtension();
                
                // Store in public/multimedia/img/avatars
                $request->avatar->move(public_path('multimedia/img/avatars'), $filename);
                
                // Save relative path to DB
                $data['avatar'] = 'multimedia/img/avatars/' . $filename;
            }

            // Handle Password Update
            if ($request->filled('password')) {
                $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            }

            // Update User (Using legacy Table update)
            // Note: Since we are using Eloquent, update() works directly if fillable is set correctly
            User::where('id', $user->id)->update($data);

            return back()->with('success', 'Perfil actualizado correctamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar perfil: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     * Public Profile View
     */
    public function show($username)
    {
        // Buscar usuario por nombre_usuario
        $user = User::where('name', $username)->firstOrFail();
        
        // Cargar anotaciones si existieran (relación pendiente)
        // $anotaciones = $user->anotaciones()->with('letra.cancion.artista')->get();
        $anotaciones = []; // Mock por ahora hasta implementar Anotaciones

        return view('usuario.show', compact('user', 'anotaciones'));
    }

    /**
     * Smart Dashboard Redirection.
     * Redirects to Artist Panel if user has artists, otherwise shows User Profile.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // If user manages any artist, redirect to Artist Panel
        if ($user->artistas->isNotEmpty()) {
            return redirect()->route('artist.panel.index');
        }

        // Otherwise show Normal Profile
        // Mock annotations until implemented
        $anotaciones = []; 
        
        return view('usuario.perfil', compact('user', 'anotaciones'));
    }

    /**
     * Redirect to the current user's profile.
     */
    public function redirectProfile()
    {
        return $this->dashboard();
    }
}
