@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilo-cancion.css') }}">
@endpush

@section('content')
<main class="contenedor-cancion">
    
    <div class="diseño-desglose">
        <aside class="panel-informacion">
            <div class="portada-cancion-grande">
                <img src="{{ $cancion->portada_url }}" alt="{{ $cancion->titulo }}">
            </div>
            
            <div class="info-metadatos">
                <h1 class="titulo-metadatos">{{ $cancion->titulo }}</h1>
                <h2 class="artista-metadatos">
                    <a href="{{ route('artista.show', $cancion->artista_id) }}" style="text-decoration: none; color: inherit;">
                        {{ $cancion->artista }}
                    </a>
                </h2>
                <div class="detalles-metadatos">
                    <p>
                        <i class="fa-solid fa-record-vinyl"></i> 
                        <a href="{{ route('album.show', $cancion->album_id) }}" style="text-decoration: none; color: inherit;">
                            {{ $cancion->album }}
                        </a>
                    </p>
                    <p><i class="fa-regular fa-clock"></i> {{ $cancion->duracion }}</p>
                </div>

                <div class="acciones-cancion" style="margin-top: 20px;">
                    @php
                        $isLikedSong = isset($likedSongs) && in_array($cancion->id, $likedSongs);
                    @endphp
                    <button class="boton-icono" onclick="toggleLike(this, 'song', '{{ $cancion->id }}', '{{ addslashes($cancion->titulo) }}', '{{ addslashes($cancion->artista) }}', '{{ $cancion->portada_url }}', '')" style="background:none; border:none; font-size: 1.8rem; cursor:pointer; padding:0; transition: transform 0.2s;">
                        <i class="fa-solid fa-heart" style="{{ $isLikedSong ? 'color: #a855f7;' : 'color: #d1d5db;' }}"></i>
                    </button>
                </div>
            </div>

            <div class="creditos-metadatos">
                <h3>Créditos</h3>
                <ul class="lista-creditos-simple">
                    @foreach($cancion->creditos as $credito)
                    <li>
                        <span class="rol">{{ $credito['rol'] }}</span>
                        <span class="nombre">{{ $credito['nombres'] }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <!-- Panel Derecho: Letra Scrolleable -->
        <section class="panel-letra" id="lyrics-panel">
            <h3 class="encabezado-letra-minimal">Letra</h3>
            
            <div class="contenido-letra-limpio position-relative" id="lyrics-content">
                @if(!empty($cancion->letra_html))
                    <div class="letra-real">
                        {!! $cancion->letra_html !!}
                    </div>
                @else
                    @foreach($cancion->letra_simulada as $parrafo)
                    <p>{{ $parrafo }}</p>
                    @endforeach
                @endif
            </div>
            
            </div>
        </section>

    </div>
</main>
@endsection
