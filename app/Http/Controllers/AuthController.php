<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Artista;
use App\Models\Genero;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Has iniciado sesión correctamente.');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|confirmed|min:4',
        ]);

        $user = User::create([
            'name' => $request->nombre_usuario,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'usuario',
        ]);

        Auth::login($user);
        return redirect('/')->with('success', 'Cuenta creada correctamente.');
    }

    public function showRegisterArtist()
    {
        return view('auth.register-artist');
    }

    public function registerArtist(Request $request)
    {
        $request->validate([
            'nombre_artistico' => ['required', 'string', 'max:255', 'unique:artista'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:4'],
            'genero_musical' => ['required', 'string'],
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->nombre_artistico,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => 'artista',
            ]);

            $artista = Artista::create([
                'nombre_artistico' => $request->nombre_artistico,
            ]);

            $genero = Genero::firstOrCreate(
                ['nombre' => $request->genero_musical]
            );

            $artista->generos()->attach($genero->genero_id);
            $user->artistas()->attach($artista->artista_id, ['fecha_asignacion' => now()]);

            DB::commit();
            Auth::login($user);

            return redirect('/')->with('success', 'Cuenta de artista creada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registro de artista fallido: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al registrar artista.'])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Sesión cerrada correctamente.');
    }
}
