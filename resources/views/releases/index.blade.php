@extends('layouts.app')

@section('title', 'Lanzamientos de la Semana')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/releases.css') }}">
@endpush

@section('content')
<div class="releases-container">
    <header class="releases-header">
        <h1 class="newspaper-title">EL ECO MUSICAL</h1>
        <div class="newspaper-date">{{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y') }}</div>
        <p class="newspaper-subtitle">Los lanzamientos más candentes de la semana, directamente a tus oídos.</p>
        <hr class="newspaper-divider">
    </header>

    @if(Auth::check() && !empty($personalizedReleases))
    <section class="personalized-section">
        <div class="section-heading">
            <h2>PARA TI</h2>
            <span>Basado en tus artistas favoritos: {{ implode(', ', array_slice($likedArtistsNames, 0, 3)) }}...</span>
        </div>
        <div class="releases-grid personalized-grid">
            @foreach(array_slice($personalizedReleases, 0, 4) as $album)
            <article class="release-card highlight-card">
                <a href="{{ $album['external_urls']['spotify'] ?? '#' }}" target="_blank" rel="noopener noreferrer">
                    <div class="release-image-wrapper">
                        @if(!empty($album['images'][0]['url']))
                            <img src="{{ $album['images'][0]['url'] }}" alt="{{ $album['name'] }}" loading="lazy">
                        @else
                            <div class="placeholder-img"><i class="fa-solid fa-compact-disc"></i></div>
                        @endif
                    </div>
                    <div class="release-info">
                        <span class="release-type">{{ strtoupper($album['album_type'] ?? 'ALBUM') }}</span>
                        <h3 class="release-title">{{ $album['name'] }}</h3>
                        <p class="release-artist">{{ $album['artists'][0]['name'] ?? 'Artista Desconocido' }}</p>
                    </div>
                </a>
            </article>
            @endforeach
        </div>
        <hr class="newspaper-divider">
    </section>
    @endif

    <section class="global-section">
        <div class="section-heading">
            <h2>NOVEDADES MUNDIALES</h2>
            <span>Lo último que está sonando en el mundo.</span>
        </div>
        
        @if(!empty($globalReleases))
            <div class="newspaper-layout">
                {{-- Destacado Principal (El primer lanzamiento) --}}
                @php $mainRelease = array_shift($globalReleases); @endphp
                @if($mainRelease)
                <article class="release-card main-feature">
                    <a href="{{ $mainRelease['external_urls']['spotify'] ?? '#' }}" target="_blank" rel="noopener noreferrer">
                        <div class="release-image-wrapper">
                            @if(!empty($mainRelease['images'][0]['url']))
                                <img src="{{ $mainRelease['images'][0]['url'] }}" alt="{{ $mainRelease['name'] }}">
                            @else
                                <div class="placeholder-img"><i class="fa-solid fa-compact-disc"></i></div>
                            @endif
                            <div class="badge-exclusive">EN PORTADA</div>
                        </div>
                        <div class="release-info">
                            <span class="release-type">{{ strtoupper($mainRelease['album_type'] ?? 'ALBUM') }}</span>
                            <h3 class="release-title headline">{{ $mainRelease['name'] }}</h3>
                            <p class="release-artist">{{ $mainRelease['artists'][0]['name'] ?? 'Artista Desconocido' }}</p>
                            <p class="release-description">El nuevo y esperado trabajo de {{ $mainRelease['artists'][0]['name'] ?? 'Artista Desconocido' }} ya está disponible. Una obra que promete revolucionar las listas de éxitos.</p>
                        </div>
                    </a>
                </article>
                @endif

                {{-- Columnas Secundarias --}}
                <div class="secondary-features">
                    @foreach(array_slice($globalReleases, 0, 4) as $album)
                    <article class="release-card secondary-card">
                        <a href="{{ $album['external_urls']['spotify'] ?? '#' }}" target="_blank" rel="noopener noreferrer">
                            <div class="release-info">
                                <span class="release-type">{{ strtoupper($album['album_type'] ?? 'ALBUM') }}</span>
                                <h3 class="release-title">{{ $album['name'] }}</h3>
                                <p class="release-artist">{{ $album['artists'][0]['name'] ?? 'Artista Desconocido' }}</p>
                            </div>
                            <div class="release-image-wrapper mini-img">
                                @if(!empty($album['images'][0]['url']))
                                    <img src="{{ $album['images'][0]['url'] }}" alt="{{ $album['name'] }}" loading="lazy">
                                @else
                                    <div class="placeholder-img"><i class="fa-solid fa-compact-disc"></i></div>
                                @endif
                            </div>
                        </a>
                    </article>
                    @endforeach
                </div>
            </div>
            
            <hr class="newspaper-divider-light">
            
            {{-- Resto de novedades en grid --}}
            <div class="releases-grid">
                @foreach(array_slice($globalReleases, 4) as $album)
                <article class="release-card normal-card">
                    <a href="{{ $album['external_urls']['spotify'] ?? '#' }}" target="_blank" rel="noopener noreferrer">
                        <div class="release-image-wrapper">
                            @if(!empty($album['images'][0]['url']))
                                <img src="{{ $album['images'][0]['url'] }}" alt="{{ $album['name'] }}" loading="lazy">
                            @else
                                <div class="placeholder-img"><i class="fa-solid fa-compact-disc"></i></div>
                            @endif
                        </div>
                        <div class="release-info">
                            <span class="release-type">{{ strtoupper($album['album_type'] ?? 'ALBUM') }}</span>
                            <h3 class="release-title">{{ $album['name'] }}</h3>
                            <p class="release-artist">{{ $album['artists'][0]['name'] ?? 'Artista Desconocido' }}</p>
                        </div>
                    </a>
                </article>
                @endforeach
            </div>
        @else
            <p style="text-align: center; padding: 50px; color: #a1a1aa;">No se encontraron novedades en este momento.</p>
        @endif
    </section>
</div>
@endsection
