<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('usuario.edit', compact('user'));
    }

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
            'avatar' => 'nullable|image|max:2048',
            'password' => 'nullable|confirmed|min:4',
        ]);

        try {
            $data = $request->only([
                'email', 'nombre_real', 'apellidos', 'calle', 'codigo_postal', 'ciudad', 'pais'
            ]);

            if ($request->hasFile('avatar')) {
                $filename = 'user_' . $user->id . '_' . time() . '.' . $request->avatar->getClientOriginalExtension();
                $request->avatar->move(public_path('multimedia/img/avatars'), $filename);
                $data['avatar'] = 'multimedia/img/avatars/' . $filename;
            }

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            User::where('id', $user->id)->update($data);
            return back()->with('success', 'Perfil actualizado correctamente.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar perfil: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($username)
    {
        $user = User::where('name', $username)->firstOrFail();
        $anotaciones = [];

        return view('usuario.show', compact('user', 'anotaciones'));
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Si usuario es artista, mostrar panel de artista
        if ($user->artistas->isNotEmpty()) {
            return redirect()->route('artist.panel.index');
        }

        $anotaciones = [];
        return view('usuario.perfil', compact('user', 'anotaciones'));
    }

    public function redirectProfile()
    {
        return $this->dashboard();
    }
}
