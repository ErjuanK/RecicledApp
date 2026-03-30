@extends('layouts.app')

@push('styles')
<style>
    body { overflow: hidden; }
</style>
@endpush

@section('content')
<div id="foryou-container" class="h-[calc(100vh-4rem)] w-full relative flex items-center justify-center overflow-hidden bg-black">
    
    <div id="background-blur" class="absolute inset-0 z-0 hidden">
        <img id="bg-img" src="" class="w-full h-full object-cover opacity-40 blur-3xl scale-110 transition-all duration-1000 transform-gpu">
        <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/40 to-black/90"></div>
    </div>

    <!-- Header Overlay -->
    <div class="absolute top-0 z-50 w-full p-6 text-center drop-shadow-lg pointer-events-none">
        <h1 class="text-2xl sm:text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-600">Para Ti</h1>
        <p class="text-xs sm:text-sm text-green-100/70 font-bold tracking-widest uppercase mt-1">Explora & Desliza</p>
    </div>

    <div id="loading-state" class="absolute inset-0 z-10 flex items-center justify-center">
        <div class="flex flex-col items-center">
            <div class="animate-spin rounded-full h-14 w-14 border-b-2 border-l-2 border-green-400 mb-6 drop-shadow-lg shadow-green-500/50"></div>
            <p class="text-gray-300 text-sm sm:text-base font-medium text-center leading-relaxed">Sintonizando tu vibra...<br>Cargando música</p>
        </div>
    </div>

    <div id="error-state" class="hidden absolute inset-0 z-10 flex items-center justify-center">
        <div class="text-center p-8 bg-black/60 rounded-3xl backdrop-blur-md border border-red-500/20 shadow-2xl mx-4">
            <p class="text-red-400 font-bold mb-6 text-lg">Nos quedamos sin ritmos</p>
            <button onclick="window.location.reload()" class="px-8 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-full font-bold shadow-xl hover:scale-105 transition-transform">Actualizar</button>
        </div>
    </div>

    <!-- Pista interactiva perfectamente centrada -->
    <div id="track-card" class="hidden relative z-10 w-full max-w-4xl px-4 sm:px-6 mx-auto flex flex-col items-center justify-center h-full max-h-screen py-24" style="user-select: none; touch-action: none; will-change: transform;">
        
        <div class="w-full relative rounded-[2rem] overflow-hidden shadow-[0_20px_60px_rgba(0,0,0,0.8)] border border-white/10 bg-black/50 backdrop-blur-sm" style="aspect-ratio: 16/9; transform-origin: center center;">
            
            <div id="swipe-right-overlay" class="opacity-0 absolute inset-0 bg-gradient-to-r from-green-500/50 to-transparent flex items-center justify-start pl-16 z-20 pointer-events-none transition-opacity duration-200">
                <div class="bg-green-500 rounded-full p-4 sm:p-5 shadow-[0_0_40px_rgba(34,197,94,0.8)] scale-110">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
            
            <div id="swipe-left-overlay" class="opacity-0 absolute inset-0 bg-gradient-to-l from-red-500/50 to-transparent flex items-center justify-end pr-16 z-20 pointer-events-none transition-opacity duration-200">
                <div class="bg-red-500 rounded-full p-4 sm:p-5 shadow-[0_0_40px_rgba(239,68,68,0.8)] scale-110">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
            </div>

            <!-- Img layers -->
            <img id="track-front" src="" class="w-full h-full object-contain absolute inset-0 z-10 drop-shadow-[0_10px_30px_rgba(0,0,0,0.7)] pointer-events-none transition-opacity duration-300" draggable="false">
            <img id="track-bg" src="" class="w-full h-full object-cover absolute inset-0 opacity-40 blur-2xl pointer-events-none transition-opacity duration-300 transform scale-105" draggable="false">

            <!-- Data overlay -->
            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/95 via-black/70 to-transparent p-6 sm:p-10 pt-32 z-10 flex justify-between items-end">
                <div class="text-left w-3/4 pointer-events-none">
                    <h2 id="t-name" class="text-3xl sm:text-5xl font-black truncate text-white drop-shadow-xl tracking-tight"></h2>
                    <p id="t-artist" class="text-xl sm:text-3xl text-green-400 font-bold truncate mt-2 drop-shadow-md"></p>
                    <p id="t-album" class="text-sm sm:text-lg text-gray-300 mt-2 truncate font-medium"></p>
                </div>
                
                <div class="relative w-14 h-14 sm:w-20 sm:h-20 flex items-center justify-center shrink-0">
                    <svg class="w-full h-full transform -rotate-90 pointer-events-none drop-shadow-lg" viewBox="0 0 36 36">
                        <path class="text-gray-600/60" stroke-width="3" stroke="currentColor" fill="none" stroke-linecap="round" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        <path id="t-progress" class="text-green-500 transition-none" stroke-width="3" stroke-linecap="round" stroke-dasharray="100, 100" stroke-dashoffset="100" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    </svg>
                    <span id="t-time" class="absolute text-sm sm:text-base font-black text-white shadow-sm drop-shadow-md">30</span>
                </div>
            </div>

            <div id="play-overlay" class="absolute inset-0 z-20 bg-black/60 flex items-center justify-center pointer-events-none hidden transition-opacity duration-300">
                <div class="bg-white/10 p-6 sm:p-8 rounded-full backdrop-blur-md shadow-2xl border border-white/20">
                    <svg class="w-16 h-16 sm:w-24 sm:h-24 text-white drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <div id="manual-controls" class="hidden absolute bottom-6 w-full flex justify-center gap-10 sm:gap-16 z-50 pointer-events-none px-6 pb-safe">
        <button id="btn-left" class="pointer-events-auto bg-gray-900 border-2 border-gray-800 hover:border-red-500/80 hover:bg-gray-800 text-red-500 rounded-full p-4 sm:p-5 transition-all duration-200 active:scale-90 shadow-[0_10px_30px_rgba(239,68,68,0.2)] flex items-center justify-center group outline-none">
            <svg class="w-8 h-8 sm:w-12 sm:h-12 transform group-active:-scale-x-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <button id="btn-right" class="pointer-events-auto bg-gray-900 border-2 border-gray-800 hover:border-green-500/80 hover:bg-gray-800 text-green-500 rounded-full p-4 sm:p-5 transition-all duration-200 active:scale-90 shadow-[0_10px_30px_rgba(34,197,94,0.2)] flex items-center justify-center group outline-none">
            <svg class="w-8 h-8 sm:w-12 sm:h-12 transform group-active:scale-x-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </button>
    </div>

    <audio id="audio-player"></audio>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let currentTrack = null;
        let playQueue = [];
        let isPreloading = false;
        let isDragging = false;
        let isAnimating = false; // Bloquea iteraciones múltiples
        let startX = 0;
        let offsetX = 0;
        const swipeThreshold = window.innerWidth > 600 ? 150 : 100;
        let initialInteracted = false;

        const ui = {
            container: document.getElementById('foryou-container'),
            loading: document.getElementById('loading-state'),
            error: document.getElementById('error-state'),
            card: document.getElementById('track-card'),
            controls: document.getElementById('manual-controls'),
            bgBlur: document.getElementById('background-blur'),
            bgImg: document.getElementById('bg-img'),
            trackFront: document.getElementById('track-front'),
            trackBg: document.getElementById('track-bg'),
            name: document.getElementById('t-name'),
            artist: document.getElementById('t-artist'),
            album: document.getElementById('t-album'),
            progress: document.getElementById('t-progress'),
            timeLabel: document.getElementById('t-time'),
            playOverlay: document.getElementById('play-overlay'),
            swipeRightOverlay: document.getElementById('swipe-right-overlay'),
            swipeLeftOverlay: document.getElementById('swipe-left-overlay'),
            audio: document.getElementById('audio-player'),
            btnLeft: document.getElementById('btn-left'),
            btnRight: document.getElementById('btn-right')
        };

        // 1. Motor de Pre-carga
        async function fetchNextTrack() {
            if(isPreloading || playQueue.length > 2) return;
            isPreloading = true;
            try {
                // Informar al backend de las canciones que ya tenemos en memoria
                let excludeIds = [currentTrack?.id, ...playQueue.map(t => t.id)].filter(Boolean);
                const res = await fetch('/api/for-you/next?exclude=' + excludeIds.join(','));
                const track = await res.json();
                if (track && !track.error && track.id) {
                    playQueue.push(track);
                    // Si hemos conseguido uno nuevo, intentamos pescar otro tras un momento
                    setTimeout(() => fetchNextTrack(), 500); 
                }
            } catch (e) {
                console.error("Fallo pre-cargando: ", e);
            } finally {
                isPreloading = false;
            }
        }

        // Configuración inicial de colas
        async function initFlow() {
            ui.error.classList.add('hidden');
            ui.card.classList.add('hidden');
            ui.controls.classList.add('hidden');
            ui.loading.classList.remove('hidden');
            
            await fetchNextTrack(); 
            
            if (playQueue.length > 0) {
                playNextFromQueue();
            } else {
                showError();
            }
        }

        function playNextFromQueue() {
            if (playQueue.length === 0) {
                // Cola vacía, mostramos loading corto
                ui.card.classList.add('hidden');
                ui.loading.classList.remove('hidden');
                // Intentamos forzar carga rápida
                fetchNextTrack().then(() => {
                    if(playQueue.length > 0) playNextFromQueue();
                    else showError();
                });
                return;
            }

            currentTrack = playQueue.shift();
            renderTrack(currentTrack);
            
            // Reposita la cola en el background
            fetchNextTrack(); 
        }

        function renderTrack(track) {
            ui.loading.classList.add('hidden');
            ui.error.classList.add('hidden');
            ui.card.classList.remove('hidden');
            ui.controls.classList.remove('hidden');
            ui.bgBlur.classList.remove('hidden');

            const imgUrl = track.album?.images[0]?.url || '';
            ui.bgImg.src = imgUrl;
            ui.trackFront.src = imgUrl;
            ui.trackBg.src = imgUrl;

            ui.name.textContent = track.name;
            ui.artist.textContent = (track.artists || []).map(a => a.name).join(', ');
            ui.album.textContent = track.album?.name || '';

            // Reiniciar Transformaciones visuales
            ui.card.style.transition = 'none';
            ui.card.style.transform = `translateX(0px) rotate(0deg)`;
            ui.swipeLeftOverlay.style.opacity = 0;
            ui.swipeRightOverlay.style.opacity = 0;

            // Motor de Audio Autoplay
            ui.audio.pause();
            if (track.preview_url) {
                ui.audio.src = track.preview_url;
                ui.audio.load();
                
                // Si el usuario ya tocó la pantalla antes, los navegadores permiten Autoplay sin problemas
                const playPromise = ui.audio.play();
                
                if (playPromise !== undefined) {
                    playPromise.then(() => {
                        ui.playOverlay.classList.add('hidden');
                    }).catch(err => {
                        console.warn('Autoplay interceptado. Solicitando interacción inicial.');
                        ui.playOverlay.classList.remove('hidden');
                    });
                }
            } else {
                ui.audio.src = '';
                ui.timeLabel.textContent = '0';
                ui.progress.style.strokeDashoffset = '100';
            }
            isAnimating = false; // Listo para swipar
        }

        function showError() {
            ui.loading.classList.add('hidden');
            ui.card.classList.add('hidden');
            ui.controls.classList.add('hidden');
            ui.error.classList.remove('hidden');
        }

        // CONTROL TÁCTIL Y DE RATÓN
        function handleStart(e) {
            if (isAnimating) return;
            isDragging = true;
            startX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
            ui.card.style.transition = 'none';
        }

        function handleMove(e) {
            if (!isDragging) return;
            // Prevenir scroll
            if(e.type.includes('touch')) e.preventDefault(); 
            
            const x = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
            offsetX = x - startX;
            // La rotación sutil acompaña al movimiento horizontal
            ui.card.style.transform = `translateX(${offsetX}px) rotate(${offsetX/15}deg)`;
            
            ui.swipeRightOverlay.style.opacity = Math.max(0, Math.min(offsetX/120, 1));
            ui.swipeLeftOverlay.style.opacity = Math.max(0, Math.min(-offsetX/120, 1));
        }

        function handleEnd(e) {
            if (!isDragging) return;
            isDragging = false;
            ui.card.style.transition = 'transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            
            if (offsetX > swipeThreshold) {
                commitSwipe('like', window.innerWidth);
            } else if (offsetX < -swipeThreshold) {
                commitSwipe('dislike', -window.innerWidth);
            } else {
                // Volver al centro
                offsetX = 0;
                ui.card.style.transform = `translateX(0px) rotate(0deg)`;
                ui.swipeRightOverlay.style.opacity = 0;
                ui.swipeLeftOverlay.style.opacity = 0;
            }
        }

        // Ejecutar Swipe
        function commitSwipe(action, endLocation) {
            if (isAnimating) return;
            isAnimating = true;
            initialInteracted = true; // El usuario ya interactuó, el próximo autoplay es libre
            ui.audio.pause();
            
            // Animación de salida brutal
            ui.card.style.transition = 'transform 0.5s ease-out';
            ui.card.style.transform = `translateX(${endLocation*1.5}px) rotate(${endLocation/10}deg)`;
            
            // Enviar a la BD en background
            sendActionToDatabase(action);

            // Transición instantánea (carga el siguiente inmediatamente de la cola)
            setTimeout(() => {
                playNextFromQueue();
            }, 350); 
        }

        async function sendActionToDatabase(action) {
            if(!currentTrack) return;
            // Necesario para evitar CSRF 419 Mismatch
            let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            try {
                // Hacer el post sin esperar, para no bloquear el UI
                fetch('/api/for-you/action', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        track_id: currentTrack.id,
                        album_id: currentTrack.album?.id,
                        action: action
                    })
                });
            } catch(e) { console.error("Error guardando swipe: ", e); }
        }

        // EVENTOS DOM
        ui.card.addEventListener('mousedown', handleStart);
        window.addEventListener('mousemove', handleMove, {passive: false}); // Capturar fuera de la tarjeta
        window.addEventListener('mouseup', handleEnd);
        
        ui.card.addEventListener('touchstart', handleStart, {passive: false});
        window.addEventListener('touchmove', handleMove, {passive: false});
        window.addEventListener('touchend', handleEnd);

        ui.btnLeft.addEventListener('click', () => commitSwipe('dislike', -window.innerWidth));
        ui.btnRight.addEventListener('click', () => commitSwipe('like', window.innerWidth));

        // EVENTOS DE AUDIO Y PROGRESO
        ui.audio.addEventListener('timeupdate', () => {
            if(!ui.audio.duration) return;
            const remaining = Math.max(0, Math.ceil(ui.audio.duration - ui.audio.currentTime)) || 0;
            ui.timeLabel.textContent = remaining;
            const percentage = (ui.audio.currentTime / ui.audio.duration);
            ui.progress.style.strokeDashoffset = isNaN(percentage) ? 100 : 100 - (percentage * 100);
        });

        // Cuando la canción acabe, hace Swipe Automático (Autoplay infinite)
        ui.audio.addEventListener('ended', () => commitSwipe('like', window.innerWidth));

        // Primera interacción destraba el audio
        document.body.addEventListener('click', () => {
            if(!initialInteracted && ui.audio.paused && currentTrack?.preview_url) {
                initialInteracted = true;
                ui.playOverlay.classList.add('hidden');
                ui.audio.play();
            }
        });

        // Arranque
        initFlow();
    });
</script>
@endpush
