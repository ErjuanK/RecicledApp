<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Laravel por defecto espera 'password', pero nuestro modelo usa 'contrasena'
        // Auth::attempt busca por 'email' y luego compara el 'password' del array con el hash de la BD.
        // Dado que nuestro User Model ya sabe que su password field es 'contrasena', 
        // Auth::attempt debería funcionar si pasamos ['email' => $email, 'password' => $inputPassword]
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/')->with('success', 'Has iniciado sesión correctamente.');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|confirmed|min:4', // confirmed busca password_confirmation
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

    /**
     * Show the artist registration form.
     */
    public function showRegisterArtist()
    {
        return view('auth.register-artist');
    }

    /**
     * Handle an incoming artist registration request.
     */
    public function registerArtist(Request $request)
    {
        $request->validate([
            'nombre_artistico' => ['required', 'string', 'max:255', 'unique:artista'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuario'],
            'password' => ['required', 'confirmed', 'min:4'],
            'genero_musical' => ['required', 'string'],
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. Create User (Role 'editor')
            $user = User::create([
                'nombre_usuario' => $request->nombre_artistico, // Use artist name as username
                'email' => $request->email,
                'contrasena' => \Illuminate\Support\Facades\Hash::make($request->password),
                'rol' => 'editor',
            ]);

            // 2. Create Artist
            $artista = \App\Models\Artista::create([
                'nombre_artistico' => $request->nombre_artistico,
            ]);

            // 3. Handle Genre
            $genero = \App\Models\Genero::firstOrCreate(
                ['nombre' => $request->genero_musical]
            );

            // 4. Link relationships
            $artista->generos()->attach($genero->genero_id);
            $user->artistas()->attach($artista->artista_id, ['fecha_asignacion' => now()]);

            \Illuminate\Support\Facades\DB::commit();

            Auth::login($user);

            return redirect('/')->with('success', 'Cuenta de artista creada correctamente.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Register Artist Failed: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return back()->withErrors(['error' => 'Error al registrar artista: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Sesión cerrada correctamente.');
    }
}
