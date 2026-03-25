@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilos-home.css') }}">
@endpush

@section('content')
<main>
    <div class="superior-centrado" style="display: flex; flex-direction: column; align-items: center; padding: 40px 20px; overflow: hidden; max-width: 100vw;">
        <h2 style="font-size: 2.2rem; font-weight: bold; margin-bottom: 40px; text-align: center;">Nuevos Lanzamientos</h2>
        <div class="novedades-full" style="width: 100%; max-width: 1000px; overflow: hidden; position: relative;">
            <div class="carrusel" id="carrusel" style="justify-content: flex-start;">
                @foreach(array_slice($albums, 0, 8) as $index => $album)
                    <div class="elemento-carrusel" data-index="{{ $index }}">
                        <a href="{{ route('album.show', $album['id']) }}">
                            <img src="{{ $album['images'][0]['url'] ?? asset('multimedia/img/Portadas/album/default.png') }}" alt="{{ $album['name'] }}">
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

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
                    <div class="visualizaciones">
                        <i class="fa-solid fa-play"></i>
                        <p>{{ number_format($track['popularity'] ?? 0 * 1000) }}</p>
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
