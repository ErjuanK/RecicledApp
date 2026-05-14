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
                    <span>Basado en tus artistas favoritos: {{ implode(', ', array_slice($likedArtistsNames, 0, 3)) }}{{ count($likedArtistsNames) > 3 ? '...' : '' }}</span>
                </div>
                <div class="releases-grid personalized-grid">
                    @foreach($personalizedReleases as $album)
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
                                    @if(!empty($album['reason']))
                                        <span class="release-reason"><i class="fa-solid fa-wand-magic-sparkles"></i> {{ $album['reason'] }}</span>
                                    @endif
                                    <span class="release-type">{{ strtoupper($album['album_type'] ?? 'ALBUM') }} &bull;
                                        {{ \Carbon\Carbon::parse($album['release_date'] ?? now())->translatedFormat('d M, Y') }}</span>
                                    <h3 class="release-title">{{ $album['name'] }}</h3>
                                    <p class="release-artist">{{ $album['artists'][0]['name'] ?? 'Artista Desconocido' }}</p>
                                    <p class="release-meta" style="font-size: 0.8rem; color: #71717a; margin-top: 5px;">
                                        {{ $album['total_tracks'] ?? 1 }} pistas
                                    </p>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if(!empty($recentEditorialReleases))
            <section class="magazine-editorial-section">
                <div class="section-heading">
                    <h2>NOTICIAS Y DESTACADOS</h2>
                </div>

                <div class="magazine-layout">
                    @php $heroArticle = $recentEditorialReleases[0] ?? null; @endphp
                    @if($heroArticle)
                    <!-- Artículo Principal (Izquierda) -->
                    <article class="magazine-hero">
                        @php
                            $heroUrl = route('album.itunes', [
                                'artist' => urlencode($heroArticle['itunes_artist'] ?? $heroArticle['artist']),
                                'album'  => urlencode($heroArticle['itunes_album']  ?? $heroArticle['title']),
                            ]);
                            $heroTooltip = 'www.recicled.app/proyectos/' . Str::slug($heroArticle['artist']) . '/' . Str::slug($heroArticle['title']);
                        @endphp
                        <a href="{{ $heroUrl }}" class="magazine-hero-image-wrapper tooltip-container" style="display: block; text-decoration: none;">
                            @if(!empty($heroArticle['cover_url']))
                                <img src="{{ $heroArticle['cover_url'] }}" alt="Portada {{ $heroArticle['title'] }}" class="magazine-hero-img">
                            @else
                                <div class="magazine-hero-img" style="background:#2d1b69; display:flex; align-items:center; justify-content:center;">
                                    <i class="fa-solid fa-compact-disc" style="font-size:5rem; color:#c084fc;"></i>
                                </div>
                            @endif
                            <div class="tooltip-content">
                                <i class="fa-solid fa-link"></i> {{ $heroTooltip }}
                            </div>
                        </a>
                        <div class="magazine-hero-text">
                            <span class="magazine-tag">RESEÑA • {{ strtoupper($heroArticle['type']) }}</span>
                            <h2 class="magazine-headline">{{ $heroArticle['description'] }}</h2>
                            <p class="magazine-description">El nuevo proyecto <strong>"{{ $heroArticle['title'] }}"</strong> de <strong>{{ $heroArticle['artist'] }}</strong> ya está dando de qué hablar.</p>
                            <div class="magazine-meta">por Redacción / {{ $heroArticle['date'] }}</div>
                            <div class="magazine-cta-container">
                                <a href="{{ $heroUrl }}" class="btn-internal-link">
                                    [VER PÁGINA DEL PROYECTO COMPLETO Y DISCOGRAFÍA]
                                </a>
                            </div>
                        </div>
                    </article>
                    @endif

                    <!-- Últimas Noticias (Derecha) -->
                    <aside class="magazine-sidebar">
                        <div class="sidebar-header">
                            <h3 class="sidebar-title">Últimas Noticias</h3>
                            <hr class="sidebar-divider">
                        </div>
                        <div class="sidebar-list">
                            @foreach(array_slice($recentEditorialReleases, 1) as $review)
                                @php
                                    $reviewUrl = route('album.itunes', [
                                        'artist' => urlencode($review['itunes_artist'] ?? $review['artist']),
                                        'album'  => urlencode($review['itunes_album']  ?? $review['title']),
                                    ]);
                                @endphp
                                <a href="{{ $reviewUrl }}" class="sidebar-card">
                                    <div class="sidebar-card-image">
                                        @if(!empty($review['cover_url']))
                                            <img src="{{ $review['cover_url'] }}" alt="Portada {{ $review['title'] }}" loading="lazy">
                                        @else
                                            <div style="width:100%;height:100%;background:#2d1b69;display:flex;align-items:center;justify-content:center;">
                                                <i class="fa-solid fa-compact-disc" style="color:#c084fc;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="sidebar-card-text">
                                        <span class="sidebar-tag">{{ strtoupper($review['type']) }}</span>
                                        <h4>{{ $review['title'] }} — {{ $review['artist'] }}</h4>
                                        <div class="sidebar-meta">{{ $review['date'] }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </aside>
                </div>
            </section>
        @endif



        @if(!empty($upcomingReleases))
            <section class="upcoming-section">
                <div class="section-heading">
                    <h2>EN EL RADAR: PRÓXIMOS LANZAMIENTOS</h2>
                </div>

                <div class="upcoming-list">
                    @foreach($upcomingReleases as $upcoming)
                        <article class="upcoming-card">
                            <div class="upcoming-date-badge">
                                <span class="upcoming-day"><i class="fa-regular fa-calendar"></i></span>
                                <span class="upcoming-month">{{ $upcoming['date'] }}</span>
                            </div>
                            <div class="upcoming-content">
                                <h3 class="upcoming-title">{{ $upcoming['title'] }}</h3>
                                <p class="upcoming-artist">{{ $upcoming['artist'] }} <span class="upcoming-type">&bull;
                                        {{ $upcoming['type'] }}</span></p>
                                <p class="upcoming-description">{{ $upcoming['description'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection