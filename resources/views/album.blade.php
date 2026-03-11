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
                    <a href="{{ route('artista.show', $album->artista_id) }}" style="text-decoration: none; color: inherit;">
                        {{ $album->artista }}
                    </a>
                </h3>
                
                <p class="descripcion-album">
                    {{ $album->descripcion }}
                </p>

                <div class="acciones-album">
                    <button class="boton-icono"><i class="fa-solid fa-heart"></i></button>
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
                <a href="{{ route('cancion.show', $cancion->id) }}" class="elemento-cancion" style="text-decoration:none; color:inherit;">
                    <span class="numero-cancion-lista">{{ $index + 1 }}</span>
                    <div class="info-cancion-lista">
                        <span class="titulo-cancion-lista">{{ $cancion->titulo }}</span>
                        <span class="artista-cancion-lista">{{ $cancion->artista }}</span>
                    </div>
                    <span class="duracion-cancion-lista">{{ $cancion->duracion }}</span>
                    <span class="opciones-cancion"><i class="fa-solid fa-ellipsis-vertical"></i></span>
                </a>
                @endforeach
            </div>
        </section>
    </div>
</main>
@endsection
