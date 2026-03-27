@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilos-home.css') }}">
@endpush

@section('content')
<main>
    <section class="novedades-showcase">
        <h2 class="novedades-titulo">Nuevos Lanzamientos</h2>
        <div class="novedades-grid">
            @foreach(array_slice($albums, 0, 3) as $index => $album)
                @php
                    $genres = array_slice($album['genres'] ?? [], 0, 3);
                    $albumName = $album['name'] ?? 'Álbum';
                    $imgUrl = $album['images'][0]['url'] ?? asset('multimedia/img/Portadas/album/default.png');
                    $albumId = $album['id'];
                    $artistName = $album['artists'][0]['name'] ?? 'Varios Artistas';
                @endphp
                <a href="{{ route('album.show', $albumId) }}" class="tarjeta-novedad">
                    <img src="{{ $imgUrl }}" alt="{{ $albumName }}" class="tarjeta-fondo">
                    <div class="tarjeta-overlay">
                        <div class="tarjeta-info">
                            <p class="tarjeta-artista">{{ $artistName }}</p>
                            <h3 class="tarjeta-nombre">{{ $albumName }}</h3>
                            @if(!empty($genres))
                                <div class="tarjeta-generos">
                                    @foreach($genres as $g)
                                        <span class="tarjeta-tag">{{ ucfirst($g) }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <div class="mid">
        <div class="encabezado-popular">
            <h2 class="popular">Lo más escuchado del mes{{ $genre ? ' (' . ucfirst($genre) . ')' : ' en España' }}</h2>
            <div class="filtro">
                <select onchange="window.location.href='?genre=' + this.value">
                    <option value="" {{ empty($genre) ? 'selected' : '' }}>Todos (Top 50 España)</option>
                    <option value="rock" {{ $genre == 'rock' ? 'selected' : '' }}>Rock</option>
                    <option value="pop" {{ $genre == 'pop' ? 'selected' : '' }}>Pop</option>
                    <option value="electronica" {{ $genre == 'electronica' ? 'selected' : '' }}>Electrónica</option>
                    <option value="reggaeton" {{ $genre == 'reggaeton' ? 'selected' : '' }}>Reggaetón</option>
                    <option value="hip hop" {{ $genre == 'hip hop' ? 'selected' : '' }}>Hip Hop</option>
                </select>
            </div>
        </div>

        <div class="cuadricula-canciones">
            @foreach($tracks as $index => $track)
                <div class="elementos">
                    <span class="numero-cancion">{{ $index + 1 }}</span>
                    <img src="{{ $track['album']['images'][0]['url'] ?? asset('multimedia/img/Portadas/album/default.png') }}" alt="{{ $track['name'] }}">
                    <p class="titulo-cancion">
                        <a href="{{ route('cancion.show', $track['id']) }}" class="enlace-discreto">{{ current(explode(' (', $track['name'])) }}</a>
                    </p>
                    <p class="artista">
                        @foreach($track['artists'] as $i => $artist)
                            <a href="{{ route('artista.show', $artist['id']) }}" class="enlace-discreto">{{ $artist['name'] }}</a>{{ $i < count($track['artists']) - 1 ? ', ' : '' }}
                        @endforeach
                    </p>
                    <div class="visualizaciones" title="Reproducciones Globales (Last.fm)">
                        <i class="fa-solid fa-eye"></i>
                        <p>{{ $track['playcount_formatted'] ?? '0' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/logica-home.js') }}"></script>
@endpush
