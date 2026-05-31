@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilo-cancion.css') }}">
    <style>
        .pagina-error-cancion {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
            padding: 3rem 1.5rem;
            gap: 1.5rem;
        }

        .error-icono-musica {
            font-size: 5rem;
            color: #6F00D0;
            opacity: 0.6;
            animation: pulsar 2.5s ease-in-out infinite;
        }

        @keyframes pulsar {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50%       { transform: scale(1.08); opacity: 1; }
        }

        .error-titulo-cancion {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0;
        }

        .error-subtitulo-cancion {
            font-size: 1.1rem;
            color: #6b7280;
            max-width: 480px;
            line-height: 1.6;
            margin: 0;
        }

        .error-id-badge {
            display: inline-block;
            background: #f3f0ff;
            border: 1px solid #d8b4fe;
            color: #7c3aed;
            font-family: monospace;
            font-size: 0.85rem;
            padding: 0.35rem 0.9rem;
            border-radius: 99px;
        }

        .botones-error-cancion {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 0.5rem;
        }

        .btn-volver {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.6rem;
            border-radius: 99px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .btn-volver:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(111,0,208,0.25);
        }

        .btn-primario {
            background: linear-gradient(135deg, #6F00D0, #a855f7);
            color: #fff;
        }

        .btn-secundario {
            background: #fff;
            color: #6F00D0;
            border: 2px solid #6F00D0;
        }

        .sugerencia-busqueda {
            margin-top: 1.5rem;
            padding: 1.25rem 2rem;
            background: #faf7ff;
            border: 1px solid #ede9fe;
            border-radius: 1rem;
            max-width: 440px;
            width: 100%;
        }

        .sugerencia-busqueda p {
            margin: 0 0 0.75rem;
            font-size: 0.9rem;
            color: #6b7280;
        }

        .sugerencia-busqueda form {
            display: flex;
            gap: 0.5rem;
        }

        .sugerencia-busqueda input {
            flex: 1;
            padding: 0.55rem 1rem;
            border: 1.5px solid #d8b4fe;
            border-radius: 99px;
            font-size: 0.9rem;
            outline: none;
            color: #1a1a2e;
            transition: border-color 0.2s;
        }

        .sugerencia-busqueda input:focus {
            border-color: #6F00D0;
        }

        .sugerencia-busqueda button {
            background: #6F00D0;
            color: #fff;
            border: none;
            border-radius: 99px;
            padding: 0.55rem 1.1rem;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .sugerencia-busqueda button:hover {
            background: #5a00ab;
        }
    </style>
@endpush

@section('content')
<main>
    <div class="pagina-error-cancion">

        <div class="error-icono-musica">
            <i class="fa-solid fa-music-slash"></i>
            {{-- Fallback si el icono no está disponible --}}
            <style>
                .fa-music-slash::before { content: "\f8d1"; }
                @supports not (content: "\f8d1") {
                    .fa-music-slash { display: none; }
                    .error-icono-musica::before {
                        content: "🎵";
                        font-style: normal;
                    }
                }
            </style>
        </div>

        <h1 class="error-titulo-cancion">Canción no encontrada</h1>

        <p class="error-subtitulo-cancion">
            No hemos podido encontrar esta canción ni en nuestra base de datos
            ni en el catálogo de iTunes. Puede que haya sido eliminada o que el
            enlace esté desactualizado.
        </p>

        @if(isset($songId))
            <span class="error-id-badge">
                <i class="fa-solid fa-tag" style="margin-right:4px;"></i>
                ID: {{ $songId }}
            </span>
        @endif

        <div class="botones-error-cancion">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}"
               class="btn-volver btn-primario">
                <i class="fa-solid fa-arrow-left"></i>
                Volver atrás
            </a>
            <a href="{{ route('home') }}" class="btn-volver btn-secundario">
                <i class="fa-solid fa-house"></i>
                Inicio
            </a>
        </div>

        <div class="sugerencia-busqueda">
            <p><i class="fa-solid fa-magnifying-glass" style="margin-right:4px;"></i>
               ¿Sabes el nombre de la canción? Búscala directamente:
            </p>
            <form onsubmit="buscarCancion(event)">
                <input type="text"
                       id="input-busqueda-error"
                       placeholder="Nombre de la canción o artista..."
                       autocomplete="off">
                <button type="submit">
                    <i class="fa-solid fa-search"></i>
                </button>
            </form>
        </div>

        <script>
        function buscarCancion(e) {
            e.preventDefault();
            const q = document.getElementById('input-busqueda-error').value.trim();
            if (q) {
                // Abre la barra de búsqueda del layout con el término rellenado
                const globalSearch = document.getElementById('search-input')
                    || document.querySelector('input[type="search"]')
                    || document.querySelector('.barra-busqueda input');
                if (globalSearch) {
                    globalSearch.value = q;
                    globalSearch.dispatchEvent(new Event('input'));
                    globalSearch.focus();
                } else {
                    // Fallback: redirige a home con hash para abrir búsqueda
                    window.location.href = '{{ route("home") }}?q=' + encodeURIComponent(q);
                }
            }
        }
        </script>

    </div>
</main>
@endsection
