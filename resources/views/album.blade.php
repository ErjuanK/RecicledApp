@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilo-album.css') }}">
@endpush

@section('content')
<main class="contenedor-album">
    <div class="banner-cabecera-album">
        <h1 class="titulo-banner-album">{{ $album->nombre }}</h1>
    </div>

    <div class="cuadricula-contenido-album">
        <!-- Columna Izquierda: Portada e Info -->
        <aside class="barra-lateral-album">
            <div class="contenedor-portada-album">
                <img src="{{ $album->portada_url }}" alt="{{ $album->nombre }}" class="img-portada-album">
            </div>
            
            <div class="detalles-album">
                <h2 class="nombre-album">{{ $album->nombre }}</h2>
                <h3 class="artista-album">
                    @if($album->artista_id)
                        <a href="{{ route('artista.show', $album->artista_id) }}" style="text-decoration: none; color: inherit;">
                            {{ $album->artista }}
                        </a>
                    @else
                        {{ $album->artista }}
                    @endif
                </h3>
                
                <p class="descripcion-album">
                    {{ $album->descripcion }}
                </p>

                <div class="acciones-album">
                    @php
                        $isLikedAlbum = isset($likedAlbums) && in_array($album->id, $likedAlbums);
                    @endphp
                    <button class="boton-icono" onclick="toggleLike(this, 'album', '{{ $album->id }}', '{{ addslashes($album->nombre) }}', '{{ addslashes($album->artista) }}', '{{ $album->portada_url }}', '')">
                        <i class="fa-solid fa-heart" style="{{ $isLikedAlbum ? 'color: #a855f7;' : '' }}"></i>
                    </button>
                    <button class="boton-icono"><i class="fa-solid fa-share"></i></button>
                </div>
            </div>
        </aside>

        <!-- Columna Derecha: Lista de Canciones -->
        <section class="canciones-album">
            <div class="cabecera-canciones">
                <span class="col-titulo">Lista de Canciones</span>
                <span class="col-encabezado-titulo">TÍTULO</span>
                <span class="col-reloj"><i class="fa-regular fa-clock"></i></span>
            </div>

            <div class="lista-canciones">
                @foreach($album->canciones as $index => $cancion)
                @php $cancionIsLocal = is_numeric($cancion->id); @endphp
                @if($cancionIsLocal)
                <a href="{{ route('cancion.show', $cancion->id) }}" class="elemento-cancion" style="text-decoration:none; color:inherit;">
                @else
                <div class="elemento-cancion">
                @endif
                    <span class="numero-cancion-lista">{{ $index + 1 }}</span>
                    <div class="info-cancion-lista">
                        <span class="titulo-cancion-lista">{{ $cancion->titulo }}</span>
                        <span class="artista-cancion-lista">{{ $cancion->artista }}</span>
                    </div>
                    <span class="duracion-cancion-lista">{{ $cancion->duracion }}</span>
                    
                    @php
                        $isLikedSong = isset($likedSongs) && in_array($cancion->id, $likedSongs);
                    @endphp
                    <span class="opciones-cancion" style="display: flex; gap: 15px; align-items: center;">
                        <button onclick="event.preventDefault(); toggleLike(this, 'song', '{{ $cancion->id }}', '{{ addslashes($cancion->titulo) }}', '{{ addslashes($cancion->artista) }}', '{{ $album->portada_url }}', '')" style="background:none; border:none; cursor:pointer; padding:0;">
                            <i class="fa-solid fa-heart" style="{{ $isLikedSong ? 'color: #a855f7;' : 'color: #999;' }}"></i>
                        </button>
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </span>
                @if($cancionIsLocal)
                </a>
                @else
                </div>
                @endif
                @endforeach
            </div>
        </section>
    </div>
</main>
@endsection
