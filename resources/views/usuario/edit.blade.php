@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilo-editar-perfil.css') }}">
@endpush

@section('content')
<div class="contenedor-editar-perfil">
    <!-- Formulario Principal -->
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="tarjeta-editar">
        @csrf
        @method('PUT')

        <h1 class="titulo-pagina">Editar Perfil</h1>

        <div class="row">
            <!-- Columna Izquierda: Avatar (Ahora col-md-3) -->
            <div class="col-md-3 columna-avatar">
                <p class="texto-foto">Foto de Perfil <i class="fa-solid fa-pen icono-edit"></i></p>
                <div class="contenedor-avatar-edit">
                    <img src="{{ $user->avatar ? asset($user->avatar) : asset('multimedia/img/default-avatar.png') }}" 
                         alt="Avatar" 
                         id="avatar-preview"
                         class="avatar-preview">
                    
                    <label for="input-avatar" class="btn-cambiar-foto">
                        <i class="fa-solid fa-camera"></i>
                    </label>
                    <input type="file" id="input-avatar" name="avatar" accept="image/*" style="display: none;" onchange="previewImage(this)">
                </div>
            </div>

            <!-- Columna Derecha: Campos -->
            <div class="col-md-9">
                <div class="grid-formulario">
                    
                    <!-- Username (Half Width to match "Nombre") -->
                    <div class="grupo-input half-width">
                        <label>Nombre de usuario <i class="fa-solid fa-pen icono-edit"></i></label>
                        <input type="text" class="input-custom" value="{{ $user->nombre_usuario }}" readonly disabled>
                    </div>

                    <!-- Empty space to align with design if needed, or just let it flow. 
                         In the design "Nombre de usuario" is alone on the top row if "Nombre" is below it.
                         If user wants "Nombre de usuario" to be half width and empty space next to it: -->
                    <div class="grupo-input half-width mobile-hidden"></div>

                    <!-- Nombre y Apellidos -->
                    <div class="grupo-input half-width">
                        <label for="nombre_real">Nombre <i class="fa-solid fa-pen icono-edit"></i></label>
                        <input type="text" name="nombre_real" id="nombre_real" class="input-custom track-change" value="{{ old('nombre_real', $user->nombre_real) }}">
                    </div>

                    <div class="grupo-input half-width">
                        <label for="apellidos">Apellidos <i class="fa-solid fa-pen icono-edit"></i></label>
                        <input type="text" name="apellidos" id="apellidos" class="input-custom track-change" value="{{ old('apellidos', $user->apellidos) }}">
                    </div>

                    <!-- Email -->
                    <div class="grupo-input full-width">
                        <label for="email">Correo electrónico <i class="fa-solid fa-pen icono-edit"></i></label>
                        <input type="text" name="email" id="email" class="input-custom track-change" value="{{ old('email', $user->email) }}">
                    </div>

                    <!-- Password -->
                    <div class="grupo-input full-width">
                        <label for="password"><i class="fa-regular fa-eye"></i> Contraseña <i class="fa-solid fa-pen icono-edit"></i></label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password" class="input-custom track-change" placeholder="Nueva contraseña (dejar vacío para no cambiar)">
                            <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility()">
                                <i class="fa-regular fa-eye" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <h3 class="titulo-seccion">Dirección y Localización</h3>

                    <div class="grupo-input half-width">
                        <label for="calle">Calle</label>
                        <input type="text" name="calle" id="calle" class="input-custom track-change" value="{{ old('calle', $user->calle) }}" placeholder="Calle ejemplo, 1">
                    </div>

                    <div class="grupo-input half-width">
                        <label for="codigo_postal">Código Postal</label>
                        <input type="text" name="codigo_postal" id="codigo_postal" class="input-custom track-change" value="{{ old('codigo_postal', $user->codigo_postal) }}" placeholder="41900">
                    </div>

                    <div class="grupo-input half-width">
                        <label for="ciudad">Ciudad</label>
                        <input type="text" name="ciudad" id="ciudad" class="input-custom track-change" value="{{ old('ciudad', $user->ciudad) }}" placeholder="Sevilla">
                    </div>

                    <div class="grupo-input half-width">
                        <label for="pais">País</label>
                        <input type="text" name="pais" id="pais" class="input-custom track-change" value="{{ old('pais', $user->pais) }}" placeholder="España">
                    </div>

                </div>

                <div class="acciones-footer">
                    <a href="{{ route('profile') }}" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Preview de Avatar
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Toggle Password
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    // Visual Cue for Edited Fields
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.track-change');
        
        inputs.forEach(input => {
            // Save initial value
            input.dataset.originalValue = input.value;
            
            input.addEventListener('input', function() {
                if (this.value !== this.dataset.originalValue) {
                    this.classList.add('is-modified');
                } else {
                    this.classList.remove('is-modified');
                }
            });
        });
    });
</script>
@endpush
