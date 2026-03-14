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
    <h1>Crea tu cuenta</h1>
    <p style="text-align: center; margin-bottom: 20px; color: var(--dark-gray);">Únete a nuestra comunidad de música.</p>

    <div class="contenedor-autenticacion">
        <form action="{{ route('register') }}" method="POST" onsubmit="return validarRegistro()">
            @csrf
            <input type="hidden" name="tipo_usuario" value="usuario">

            <label for="nombre">Nombre de usuario</label>
            <div class="grupo-entrada">
                <i class="fa-regular fa-user icono-izquierda"></i>
                <input type="text" id="nombre" name="nombre_usuario" placeholder="Introduce tu nombre de usuario" value="{{ old('nombre_usuario') }}" required autofocus>
            </div>
            @error('nombre_usuario')
                <span class="text-red-500 text-sm block mt-1 mb-2">{{ $message }}</span>
            @enderror

            <label for="email">Correo electrónico</label>
            <div class="grupo-entrada">
                <i class="fa-regular fa-envelope icono-izquierda"></i>
                <input type="email" id="email" name="email" placeholder="Introduce tu correo electrónico" value="{{ old('email') }}" required>
            </div>
            @error('email')
                <span class="text-red-500 text-sm block mt-1 mb-2">{{ $message }}</span>
            @enderror

            <label for="password">Contraseña</label>
            <div class="grupo-entrada">
                <span class="material-symbols-outlined icono-izquierda">lock</span>
                <div class="contenedor-input-icono">
                    <input type="password" id="password" name="password" placeholder="Introduce tu contraseña" required>
                    <i class="fa-solid fa-eye alternar-contrasena" style="cursor: pointer;"></i>
                </div>
            </div>
            @error('password')
                <span class="text-red-500 text-sm block mt-1 mb-2">{{ $message }}</span>
            @enderror

            <label for="password_confirmation">Confirmar contraseña</label>
            <div class="grupo-entrada">
                <span class="material-symbols-outlined icono-izquierda">lock</span>
                <div class="contenedor-input-icono">
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirma tu contraseña" required>
                    <i class="fa-solid fa-eye alternar-contrasena" style="cursor: pointer;"></i>
                </div>
            </div>

            <button type="submit" class="boton-enviar">Registrarse</button>
            
            <div style="text-align: center; margin-top: 15px;">
                <span style="color: var(--dark-gray); font-size: 0.9em;">¿Eres un artista?</span>
                <!-- TODO: Crear ruta de registro de artista -->
                <a href="{{ route('register.artist') }}" style="color: var(--primary-purple); font-weight: bold; font-size: 0.9em; text-decoration: none;">Crea tu cuenta de artista aquí</a>
            </div>
        </form>
        
        <p style="font-size: 0.8em; text-align: center; margin-top: 15px; color: var(--dark-gray);">
            Al registrarte, aceptas nuestra <a href="#" style="color: var(--primary-purple);">Política de Privacidad</a> y <a href="#" style="color: var(--primary-purple);">Términos de Servicio</a>.
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... (other scripts if any, but toggle logic is removed)
    });

    function validarRegistro() {
        const pass = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirmation').value;
        if (pass !== confirm) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Las contraseñas no coinciden',
                confirmButtonColor: '#d33'
            });
            return false;
        }
        return true;
    }
</script>
@endpush
