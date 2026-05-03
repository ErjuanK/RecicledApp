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
            <section class="genius-editorial-section">
                <div class="section-heading">
                    <h2>NOTICIAS Y DESTACADOS</h2>
                </div>

                @php
                    // Extract the first item as the hero feature
                    $featuredEditorial = array_shift($recentEditorialReleases);
                @endphp

                <a href="#" class="genius-hero" style="text-decoration: none; color: inherit; display: flex;">
                    <div class="genius-hero-text">
                        <span class="genius-tag">RESEÑA • {{ strtoupper($featuredEditorial['type']) }}</span>
                        <h2>{{ $featuredEditorial['description'] }}</h2>
                        <p>El nuevo proyecto "{{ $featuredEditorial['title'] }}" de
                            <strong>{{ $featuredEditorial['artist'] }}</strong> ya está dando de qué hablar.
                        </p>
                        <div class="genius-meta">por Redacción / {{ $featuredEditorial['date'] }}</div>
                    </div>
                    <div class="genius-hero-image">
                        <img src="https://picsum.photos/seed/{{ md5($featuredEditorial['title']) }}/800/450"
                            alt="Portada {{ $featuredEditorial['title'] }}">
                    </div>
                </a>

                <div class="genius-grid">
                    @foreach($recentEditorialReleases as $review)
                        <a href="#" class="genius-card" style="text-decoration: none; color: inherit;">
                            <div class="genius-card-text">
                                <span class="genius-tag">{{ strtoupper($review['type']) }}</span>
                                <h3>{{ $review['description'] }}</h3>
                                <div class="genius-meta">por Redacción / {{ $review['date'] }}</div>
                            </div>
                            <div class="genius-card-image">
                                <img src="https://picsum.photos/seed/{{ md5($review['title']) }}/400/400"
                                    alt="Portada {{ $review['title'] }}">
                            </div>
                        </a>
                    @endforeach
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