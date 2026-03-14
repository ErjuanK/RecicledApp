@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/styleLogin.css') }}?v=1.3">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=lock" />
    <style>
    .material-symbols-outlined {
      font-variation-settings:
      'FILL' 0,
      'wght' 400,
      'GRAD' 0,
      'opsz' 24
    }
    </style>
@endpush

@section('content')
<div class="auth-wrapper">
    <h1>Registro para Artistas</h1>
    <p style="text-align: center; margin-bottom: 20px; color: var(--dark-gray);">Únete como creador y comparte tu música.</p>

    <div class="contenedor-autenticacion">
        <form method="POST" action="{{ route('register.artist.submit') }}">
            @csrf

            <!-- Nombre Artístico -->
            <label for="nombre_artistico">Nombre Artístico</label>
            <div class="grupo-entrada">
                <i class="fa-solid fa-microphone icono-izquierda"></i>
                <input id="nombre_artistico" type="text" name="nombre_artistico" value="{{ old('nombre_artistico') }}" placeholder="Ej. Bad Bunny" required autofocus>
            </div>
            @error('nombre_artistico')
                <span style="color:red; font-size: 0.9em; display:block; margin-bottom:10px;">{{ $message }}</span>
            @enderror

            <!-- Género Musical -->
            <label for="genero_musical">Género Principal</label>
            <div class="grupo-entrada">
                <i class="fa-solid fa-music icono-izquierda"></i>
                <select id="genero_musical" name="genero_musical" required style="width: 100%; padding: 0.8em; padding-left: 2.5em; border: 1px solid var(--medium-gray); border-radius: 8px; color: var(--text-color);">
                    <option value="" selected disabled>Selecciona un género...</option>
                    <option value="Reggaeton">Reggaeton</option>
                    <option value="Pop">Pop</option>
                    <option value="Rock">Rock</option>
                    <option value="Trap">Trap</option>
                    <option value="Hip Hop">Hip Hop</option>
                    <option value="Indie">Indie</option>
                    <option value="R&B">R&B</option>
                    <option value="Latino">Latino</option>
                </select>
            </div>
            @error('genero_musical')
                <span style="color:red; font-size: 0.9em; display:block; margin-bottom:10px;">{{ $message }}</span>
            @enderror

            <!-- Email -->
            <label for="email">Correo Electrónico</label>
            <div class="grupo-entrada">
                <i class="fa-regular fa-envelope icono-izquierda"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nombre@ejemplo.com" required>
            </div>
            @error('email')
                <span style="color:red; font-size: 0.9em; display:block; margin-bottom:10px;">{{ $message }}</span>
            @enderror

            <!-- Contraseña -->
            <label for="password">Contraseña</label>
            <div class="grupo-entrada">
                <span class="material-symbols-outlined icono-izquierda">lock</span>
                <div class="contenedor-input-icono">
                    <input id="password" type="password" name="password" placeholder="Crea tu contraseña" required>
                    <i class="fa-solid fa-eye alternar-contrasena" style="cursor: pointer;"></i>
                </div>
            </div>
            @error('password')
                <span style="color:red; font-size: 0.9em; display:block; margin-bottom:10px;">{{ $message }}</span>
            @enderror

            <!-- Confirmar Contraseña -->
            <label for="password-confirm">Confirmar Contraseña</label>
            <div class="grupo-entrada">
                <span class="material-symbols-outlined icono-izquierda">lock</span>
                <div class="contenedor-input-icono">
                    <input id="password-confirm" type="password" name="password_confirmation" placeholder="Confirma tu contraseña" required>
                    <i class="fa-solid fa-eye alternar-contrasena" style="cursor: pointer;"></i>
                </div>
            </div>

            <button type="submit" class="boton-enviar">Registrarse como Artista</button>
            
            <div style="text-align: center; margin-top: 15px;">
                <span style="color: var(--dark-gray); font-size: 0.9em;">¿Ya tienes cuenta?</span>
                <a href="{{ route('login') }}" style="color: var(--primary-purple); font-weight: bold; font-size: 0.9em; text-decoration: none;">Inicia sesión</a>
            </div>
        </form>
        
        <p style="font-size: 0.8em; text-align: center; margin-top: 15px; color: var(--dark-gray);">
            Al registrarte, aceptas nuestra <a href="#" style="color: var(--primary-purple);">Política de Privacidad</a> y <a href="#" style="color: var(--primary-purple);">Términos de Servicio</a>.
        </p>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script moved to interacciones-formulario.js --}}
@endpush
