@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilo-perfil.css') }}">
@endpush

@section('content')
<div class="contenedor-perfil">
    <!-- Section: User Info -->
    <div class="tarjeta-perfil-header">
        <div class="info-usuario-izquierda">
            <div class="avatar-grande-contenedor">
                <img src="{{ $user->avatar ? asset($user->avatar) : asset('multimedia/img/default-avatar.png') }}" alt="Avatar" class="avatar-grande" onerror="this.src='https://via.placeholder.com/150'">
            </div>
            <div class="datos-texto-usuario">
                <h1>{{ $user->nombre_real ?? $user->name }}</h1>
                <p class="email-usuario">{{ $user->email }}</p>
                @if (!empty($user->ciudad) || !empty($user->pais))
                    <p class="email-usuario" style="margin-top: 5px; font-size: 0.9rem;">
                        <i class="fa-solid fa-location-dot" style="margin-right: 5px;"></i>
                        {{ trim(($user->ciudad ? $user->ciudad : '') . ($user->ciudad && $user->pais ? ', ' : '') . ($user->pais ? $user->pais : '')) }}
                    </p>
                @endif
            </div>
        </div>
        <div class="acciones-usuario-derecha">
            <a href="{{ route('profile.edit') }}" class="btn-editar-perfil">
                <i class="fa-solid fa-pen"></i> Editar Perfil
            </a>
            <div class="icono-favoritos-contenedor">
                <i class="fa-solid fa-heart icono-favoritos"></i>
            </div>
        </div>
    </div>

    <!-- Section: My Annotations -->
    <div class="seccion-anotaciones">
        <h2>Mis Anotaciones</h2>
        
        @if (empty($anotaciones))
            <div class="mensaje-vacio">
                <p>No hay anotaciones disponibles</p>
            </div>
        @else
            <div class="lista-anotaciones">
                @foreach ($anotaciones as $nota)
                    <div class="tarjeta-anotacion">
                        <div class="borde-lateral-morado"></div>
                        <div class="contenido-anotacion">
                            <p class="meta-anotacion">
                                Anotación en <span class="resaltado-morado">{{ $nota->cancion_titulo ?? 'Canción' }}</span> de <span class="resaltado-morado">{{ $nota->artista_nombre ?? 'Artista' }}</span>
                            </p>
                            <blockquote class="texto-anotacion">
                                "{{ $nota->texto ?? 'Texto de anotación...' }}"
                            </blockquote>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="ver-todas-contenedor">
                <a href="#" class="enlace-ver-todas">Ver todas mis anotaciones</a>
            </div>
        @endif
    </div>

    <!-- Section: Account Settings -->
    <div class="seccion-configuracion">
        <h3>Configuración de la Cuenta</h3>
        <div class="grid-configuracion">
            <!-- Card 1: Change Password -->
            <a href="{{ route('profile.edit') }}" class="tarjeta-configuracion">
                <div class="icono-configuracion-contenedor">
                    <i class="fa-solid fa-key"></i>
                </div>
                <div class="texto-configuracion">
                    <h4>Cambiar Contraseña</h4>
                    <p>Actualiza la seguridad de tu cuenta.</p>
                </div>
            </a>

            <!-- Card 2: Notifications -->
            <div class="tarjeta-configuracion">
                <div class="icono-configuracion-contenedor">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div class="texto-configuracion">
                    <h4>Notificaciones</h4>
                    <p>Gestiona tus notificaciones.</p>
                </div>
            </div>

            <!-- Card 3: Delete Account -->
            <div class="tarjeta-configuracion peligro">
                <div class="icono-configuracion-contenedor rojo">
                    <i class="fa-solid fa-trash-can"></i>
                </div>
                <div class="texto-configuracion">
                    <h4 class="texto-rojo">Eliminar Cuenta</h4>
                    <p class="texto-rojo-suave">Esta acción es irreversible.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Verificar si hay parámetros de éxito en la URL (al igual que en el layout original)
    // Aunque en Laravel usamos session('success'), mantenemos esto por compatibilidad si es necesario
    // o lo adaptamos a SweetAlert con blade.
    @if(session('success'))
        Swal.fire({
            title: '¡Guardado!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#6F00D0',
            confirmButtonText: 'Genial'
        });
    @endif
</script>
@endpush
