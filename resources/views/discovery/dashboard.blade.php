@extends('layouts.discovery')

@section('title', 'Tu Mix Semanal')

@section('content')
<div x-data="musicPlayer()" class="pb-28">

    {{-- ===== HERO SECTION: Tu Mix Semanal ===== --}}
    <section class="relative overflow-hidden">
        {{-- Animated Gradient Background --}}
        <div class="absolute inset-0 bg-gradient-to-br from-purple-900 via-indigo-900 to-fuchsia-900 animate-gradient opacity-90"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-surface via-transparent to-transparent"></div>

        {{-- Floating Orbs --}}
        <div class="absolute top-20 left-10 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-10 right-20 w-48 h-48 bg-fuchsia-500/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 pt-12 pb-16">
            {{-- Nav --}}
            <nav class="flex items-center justify-between mb-16">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-accent/20 flex items-center justify-center group-hover:bg-accent/30 transition-colors">
                        <svg class="w-5 h-5 text-accent-light" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2s3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2s3 .895 3 2z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">Music<span class="text-accent-light">Discovery</span></span>
                </a>
                <div class="flex items-center gap-2">
                    @foreach($genres as $genre)
                    <span class="px-3 py-1 text-xs font-medium rounded-full glass text-purple-200">{{ ucfirst($genre) }}</span>
                    @endforeach
                </div>
            </nav>

            {{-- Hero Content --}}
            <div class="flex flex-col lg:flex-row items-center gap-12">
                {{-- Left: Info --}}
                <div class="flex-1 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full glass text-xs font-medium text-purple-200">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        Generado con IA
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-black leading-tight">
                        Tu Mix<br>
                        <span class="bg-gradient-to-r from-purple-400 via-fuchsia-400 to-pink-400 bg-clip-text text-transparent">Semanal</span>
                    </h1>
                    <p class="text-lg text-gray-300 max-w-md">
                        {{ count($playlist) }} canciones seleccionadas especialmente para ti, basadas en tus gustos musicales.
                    </p>
                    <div class="flex items-center gap-4">
                        <button @click="playAll()" class="px-8 py-3.5 bg-accent hover:bg-accent-dark rounded-full font-bold text-sm tracking-wide transition-all hover:scale-105 pulse-glow flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            Reproducir Todo
                        </button>
                        <button class="px-6 py-3.5 glass hover:bg-white/10 rounded-full font-medium text-sm transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/></svg>
                            Compartir
                        </button>
                    </div>
                </div>

                {{-- Right: Playlist Preview --}}
                <div class="flex-1 w-full max-w-lg">
                    <div class="glass rounded-2xl p-5 space-y-1">
                        @foreach(array_slice($playlist, 0, 5) as $index => $track)
                        <div @click="play({{ json_encode($track) }})"
                             class="flex items-center gap-4 p-3 rounded-xl cursor-pointer transition-all duration-200"
                             :class="currentTrack?.id === {{ $track['id'] }} ? 'bg-accent/20 ring-1 ring-accent/30' : 'hover:bg-white/5'">
                            <span class="text-sm font-medium text-gray-500 w-6 text-right">{{ $index + 1 }}</span>
                            <img src="{{ $track['cover'] }}" alt="" class="w-11 h-11 rounded-lg object-cover shadow-lg">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-white truncate">{{ $track['title'] }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $track['artist'] }}</p>
                            </div>
                            <span class="text-xs text-gray-500 font-medium">{{ $track['duration'] }}</span>
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-5 h-5 text-accent-light" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                        @endforeach

                        @if(count($playlist) > 5)
                        <div class="pt-2 text-center">
                            <button @click="showAllPlaylist = !showAllPlaylist" class="text-xs font-medium text-accent-light hover:text-white transition-colors">
                                <span x-text="showAllPlaylist ? 'Mostrar menos' : 'Ver las {{ count($playlist) }} canciones'"></span>
                            </button>
                        </div>
                        @endif

                        {{-- Expanded Playlist --}}
                        <template x-if="showAllPlaylist">
                            <div class="space-y-1 pt-1">
                                @foreach(array_slice($playlist, 5) as $index => $track)
                                <div @click="play({{ json_encode($track) }})"
                                     class="flex items-center gap-4 p-3 rounded-xl cursor-pointer transition-all duration-200"
                                     :class="currentTrack?.id === {{ $track['id'] }} ? 'bg-accent/20 ring-1 ring-accent/30' : 'hover:bg-white/5'">
                                    <span class="text-sm font-medium text-gray-500 w-6 text-right">{{ $index + 6 }}</span>
                                    <img src="{{ $track['cover'] }}" alt="" class="w-11 h-11 rounded-lg object-cover shadow-lg">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-white truncate">{{ $track['title'] }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ $track['artist'] }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500 font-medium">{{ $track['duration'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== ALBUMS SECTION ===== --}}
    <section class="max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-white">Álbumes para Ti</h2>
                <p class="text-gray-400 mt-1">Basado en tus artistas favoritos</p>
            </div>
            <button class="text-sm font-medium text-accent-light hover:text-white transition-colors">Ver todos →</button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($albums as $album)
            <div @click="play({ id: {{ $album['id'] }}, title: '{{ addslashes($album['title']) }}', artist: '{{ addslashes($album['artist']) }}', duration: '{{ $album['tracks'] }} tracks', cover: '{{ $album['cover'] }}' })"
                 class="group cursor-pointer">
                <div class="relative overflow-hidden rounded-xl shadow-lg mb-4">
                    <img src="{{ $album['cover'] }}" alt="{{ $album['title'] }}"
                         class="w-full aspect-square object-cover transition-transform duration-500 group-hover:scale-105">
                    {{-- Overlay on hover --}}
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all duration-300 flex items-center justify-center">
                        <div class="w-14 h-14 bg-accent rounded-full flex items-center justify-center shadow-2xl opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                            <svg class="w-6 h-6 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                </div>
                <h3 class="font-bold text-white text-sm truncate group-hover:text-accent-light transition-colors">{{ $album['title'] }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ $album['artist'] }} · {{ $album['year'] }}</p>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ===== JOYAS OCULTAS (Singles) ===== --}}
    <section class="max-w-7xl mx-auto px-6 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-white">Joyas Ocultas</h2>
                <p class="text-gray-400 mt-1">Singles que podrían convertirse en tus favoritos</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($singles as $single)
            <div @click="play({{ json_encode($single) }})"
                 class="glass rounded-xl p-4 flex items-center gap-4 cursor-pointer hover:bg-white/10 transition-all duration-300 group"
                 :class="currentTrack?.id === {{ $single['id'] }} ? 'ring-1 ring-accent/40 bg-accent/10' : ''">
                <div class="relative flex-shrink-0">
                    <img src="{{ $single['cover'] }}" alt="" class="w-16 h-16 rounded-lg object-cover shadow-lg group-hover:shadow-accent/20 transition-shadow">
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="w-8 h-8 bg-accent/90 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-white text-sm truncate group-hover:text-accent-light transition-colors">{{ $single['title'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $single['artist'] }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-xs text-gray-500 font-medium">{{ $single['duration'] }}</span>
                    <div class="mt-1">
                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-full bg-fuchsia-500/20 text-fuchsia-300 uppercase tracking-wider">Single</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- Reproductor eliminado en la vista Discover (flotante eliminado) --}}
</div>

<script>
function musicPlayer() {
    return {
        currentTrack: null,
        isPlaying: false,
        progress: 0,
        currentTime: '0:00',
        showAllPlaylist: false,
        progressInterval: null,
        allTracks: @json($playlist),

        play(track) {
            this.currentTrack = track;
            this.isPlaying = true;
            this.progress = 0;
            this.currentTime = '0:00';
            this.simulateProgress();
        },

        playAll() {
            if (this.allTracks.length > 0) {
                this.play(this.allTracks[0]);
            }
        },

        togglePlay() {
            this.isPlaying = !this.isPlaying;
            if (this.isPlaying) {
                this.simulateProgress();
            } else {
                clearInterval(this.progressInterval);
            }
        },

        stop() {
            this.currentTrack = null;
            this.isPlaying = false;
            this.progress = 0;
            clearInterval(this.progressInterval);
        },

        nextTrack() {
            if (!this.currentTrack) return;
            const idx = this.allTracks.findIndex(t => t.id === this.currentTrack.id);
            if (idx >= 0 && idx < this.allTracks.length - 1) {
                this.play(this.allTracks[idx + 1]);
            }
        },

        previousTrack() {
            if (!this.currentTrack) return;
            const idx = this.allTracks.findIndex(t => t.id === this.currentTrack.id);
            if (idx > 0) {
                this.play(this.allTracks[idx - 1]);
            }
        },

        simulateProgress() {
            clearInterval(this.progressInterval);
            this.progressInterval = setInterval(() => {
                if (this.progress < 100) {
                    this.progress += 0.5;
                    const totalSeconds = Math.floor(this.progress * 2.4);
                    const mins = Math.floor(totalSeconds / 60);
                    const secs = totalSeconds % 60;
                    this.currentTime = mins + ':' + String(secs).padStart(2, '0');
                } else {
                    this.nextTrack();
                }
            }, 1000);
        }
    };
}
</script>
@endsection
