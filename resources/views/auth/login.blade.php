@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/styleLogin.css') }}?v=1.3">
@endpush

@section('content')
<div class="auth-wrapper">
    <h1>Inicia Sesión</h1>

    <div class="contenedor-autenticacion">
        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <label for="email">Correo Electrónico</label>
            <div class="grupo-entrada">
                <i class="fa-regular fa-envelope icono-izquierda"></i>
                <input type="email" id="email" name="email" placeholder="Introduce tu correo electrónico" value="{{ old('email') }}" required autofocus>
            </div>
            @error('email')
                <span style="color:red; font-size: 0.9em;">{{ $message }}</span>
            @enderror

            <label for="password">Contraseña</label>
            <div class="grupo-entrada">
                <i class="fa-solid fa-lock icono-izquierda"></i>
                <div class="contenedor-input-icono">
                    <input type="password" id="password" name="password" placeholder="Introduce tu contraseña" required>
                    <i class="fa-solid fa-eye alternar-contrasena icono-derecha"></i>
                </div>
            </div>
            @error('password')
                <span style="color:red; font-size: 0.9em;">{{ $message }}</span>
            @enderror

            <a href="#" class="olvido-contrasena">¿Olvidaste tu contraseña?</a>

            <button type="submit" class="boton-enviar">Iniciar Sesión</button>

            <div class="separador">
                <span>O inicia sesión con</span>
            </div>

            <div class="inicio-social">
                <button type="button" class="boton-social"><i class="fa-brands fa-google google-icon"></i></button>
                <button type="button" class="boton-social"><i class="fa-brands fa-apple"></i></button>
            </div>
        </form>
    </div>

    <p class="enlace-registro">
        ¿Aún no tienes una cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
    </p>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejo de SweetAlerts desde sesión de Laravel
        @if(session('success'))
            const successType = "{{ session('success') }}";
            // Lógica genérica por defecto, se puede personalizar más si el controlador envía 'types' específicos
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: successType,
                confirmButtonColor: '#6F00D0'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                confirmButtonColor: '#d33'
            });
        @endif
    });
</script>
@endpush
