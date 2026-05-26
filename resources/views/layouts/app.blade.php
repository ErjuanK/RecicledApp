<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>R E C I C L E D ☆ — @yield('title', 'Tu Música')</title>
    <link rel="shortcut icon" href="{{ asset('multimedia/img/logo_3d.ico') }}" type="image/x-icon">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/interactividad.css') }}">
    <link rel="stylesheet" href="{{ asset('css/busqueda.css') }}">
    <link rel="stylesheet" href="{{ asset('css/estilos-globales.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/interacciones-formulario.js') }}" defer></script>
    
    @stack('scripts')
    <script>
        window.searchRoute = "{{ route('api.buscar') }}";
    </script>
</head>
<body class="bg-gray-100">
    <header>
        <div class="header-left">
            <div class="contenedor-busqueda buscador">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="campo-busqueda" placeholder="Buscar artistas o canciones...">
                <div id="resultados-busqueda" class="oculto"></div>
            </div>
        </div>
        <div class="header-center">
            <div class="img">
                <a href="{{ url('/') }}" style="display:flex; align-items:center; text-decoration:none; gap: 10px;">
                    <img src="{{ asset('multimedia/img/logo_White.png') }}" alt="Project Icon" style="height: 80px; width: 80px; object-fit: contain;">
                </a>
            </div>
        </div>
        <div class="header-right">
            <style>
                /* Estilos para el Dropdown de Usuario - Portado a Blade */
                .usuario-dropdown-contenedor {
                    position: relative;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    cursor: pointer;
                    padding: 5px 10px;
                    border-radius: 20px;
                    transition: background-color 0.2s;
                }
                .usuario-dropdown-contenedor:hover {
                    background-color: rgba(255, 255, 255, 0.1);
                }
                .avatar-mini {
                    width: 35px;
                    height: 35px;
                    border-radius: 50%;
                    object-fit: cover;
                    border: 2px solid white;
                }
                .nombre-usuario-header {
                    color: white;
                    font-weight: 600;
                    font-size: 0.95em;
                }
                .menu-desplegable {
                    display: none;
                    position: absolute;
                    top: 100%;
                    right: 0;
                    background-color: white;
                    min-width: 180px;
                    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
                    border-radius: 8px;
                    z-index: 1000;
                    overflow: hidden;
                    margin-top: 10px;
                }
                /* Puente invisible */
                .usuario-dropdown-contenedor::after {
                    content: '';
                    position: absolute;
                    top: 100%;
                    left: 0;
                    width: 100%;
                    height: 10px; 
                }
                .usuario-dropdown-contenedor:hover .menu-desplegable {
                    display: block;
                }
                .menu-desplegable a {
                    color: #333;
                    padding: 12px 16px;
                    text-decoration: none;
                    display: block;
                    font-size: 0.9em;
                    transition: background-color 0.2s;
                }
                .menu-desplegable a:hover {
                    background-color: #f1f1f1;
                    color: #6F00D0;
                }
                .menu-desplegable a i {
                    margin-right: 8px;
                    width: 20px;
                    text-align: center;
                }
            </style>
            
            <div class="usuario-dropdown-contenedor">
                @auth
                    @if(Auth::user()->avatar)
                        <img src="{{ asset(Auth::user()->avatar) }}" 
                             alt="User" 
                             class="avatar-mini"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                        <i class="fa-solid fa-user" style="color: white; font-size: 1.5em; display: none;"></i>
                    @else
                        <i class="fa-solid fa-user" style="color: white; font-size: 1.5em;"></i>
                    @endif
                    
                    <span class="nombre-usuario-header">{{ Auth::user()->name }}</span>
                    <i class="fa-solid fa-chevron-down" style="color: white; font-size: 0.8em;"></i>
                @else
                    <i class="fa-solid fa-user" style="color: white; font-size: 1.5em;"></i>
                @endauth

                <div class="menu-desplegable">
                    @auth
                        <a href="{{ route('profile') }}">
                            <i class="fa-solid fa-user"></i> Mi Perfil
                        </a>
                        <a href="{{ route('likes.index') }}">
                            <i class="fa-solid fa-heart" style="color:#a855f7"></i> Mis Me Gustas
                        </a>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                            </a>
                        </form>
                    @else
                        <a href="{{ route('login') }}">
                            <i class="fa-solid fa-right-to-bracket"></i> Inicia Sesión
                        </a>
                        <a href="{{ route('register') }}">
                            <i class="fa-solid fa-user-plus"></i> Registrarse
                        </a>
                    @endauth
                </div>
            </div>
        </div>
        <button class="btn-hamburguesa" id="btn-hamburguesa" aria-label="Menú">
            <i class="fa-solid fa-bars"></i>
        </button>
        
    </header>
    <div class="navegacion">
        <nav>
            <a href="{{ route('releases.index') }}">LANZAMIENTOS</a>
            <a href="{{ url('/discovery') }}">DESCUBRIMIENTOS</a>
            <a href="{{ url('/for-you') }}">PARA TI</a>

        </nav>
    </div>

    <!-- Contenido Principal -->
    <main>
        @yield('content')
    </main>

    <footer>
        <div class="cuadricula-pie">
            <div class="pie p1">
                <div class="imagen-pie">
                    <img src="{{ asset('multimedia/img/logo_3d.png') }}" alt="Footer Logo">
                </div>
                <p>Explora tus gustos, encuentra nuevas canciones y sumérgete en cada género. Conoce tu música, con contexto y emoción.</p>
            </div>

            <div class="pie p2">
                <div class="titulo-footer">
                    <h3>Compañía</h3>
                </div>
                <div class="text-footer">
                    <a href="#">Acerca de</a>
                    <a href="#">Contactos</a>
                </div>
            </div>

            <div class="pie p3">
                <div class="titulo-footer">
                    <h3>Legal</h3>
                </div>
                <div class="text-footer">
                    <a href="#">Términos y Condiciones</a>
                    <a href="#">Política de Privacidad</a>
                </div>
            </div>

            <div class="pie p4">
                <div class="titulo-footer">
                    <h3>Donde Encontrarnos</h3>
                </div>
                <div class="redes-sociales">
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="far fa-envelope"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                    <a href="#"><i class="fab fa-x"></i></a>
                </div>
            </div>
        </div>
    </footer>
    <script src="{{ asset('js/script.js') }}?v=4"></script>
    <script src="{{ asset('js/busqueda.js') }}?v=4"></script>
    <script src="{{ asset('js/busqueda-artista.js') }}?v=4"></script>

    {{-- Toggle menú móvil --}}
    <script>
        (function() {
            const btnHamburguesa = document.getElementById('btn-hamburguesa');
            const navMovil = document.querySelector('.navegacion');
            if (btnHamburguesa && navMovil) {
                btnHamburguesa.addEventListener('click', function() {
                    navMovil.classList.toggle('activa');
                    // Cambiar icono entre hamburguesa y X
                    const icono = btnHamburguesa.querySelector('i');
                    if (navMovil.classList.contains('activa')) {
                        icono.classList.remove('fa-bars');
                        icono.classList.add('fa-xmark');
                    } else {
                        icono.classList.remove('fa-xmark');
                        icono.classList.add('fa-bars');
                    }
                });
            }
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuración común para SweetAlert2
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Alerta de Éxito (Session)
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#6F00D0',
                    confirmButtonText: 'Genial'
                });
            @endif

            // Alerta de Error (Session)
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Entendido'
                });
            @endif

            // Alerta de Errores de Validación (Laravel $errors)
            @if($errors->any())
                let mensajes = '';
                @foreach($errors->all() as $error)
                    mensajes += '{{ $error }}<br>';
                @endforeach
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Por favor revisa los campos',
                    html: mensajes,
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'Revisar'
                });
            @endif
        });

        // Lógica Global de "Me Gustas"
        window.isLoggedIn = @json(Auth::check());
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function toggleLike(btn, type, spotify_id, name, artist_name = '', image_url = '', external_url = '') {
            if (!window.isLoggedIn) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Inicia sesión',
                    text: 'Debes estar logueado para poder guardar tus favoritos.',
                    confirmButtonColor: '#6F00D0',
                    confirmButtonText: 'Ir a Login',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route('login') }}';
                    }
                });
                return;
            }

            try {
                const response = await fetch('{{ route('likes.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    },
                    body: JSON.stringify({
                        type, spotify_id, name, artist_name, image_url, external_url
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    const icon = btn.querySelector('i');
                    if (data.liked) {
                        icon.style.color = '#a855f7'; // Color de "Me gusta"
                    } else {
                        icon.style.color = ''; // Volver al color por defecto
                    }
                }
            } catch (error) {
                console.error("Error toggling like", error);
            }
        }
    </script>
</body>
</html>
