<?php $__env->startPush('styles'); ?>
<style>
    /* Eliminar el padding global de main y el fondo blanco del body en esta página */
    body  { overflow: hidden; background: #0d001a !important; }
    main  { padding: 0 !important; background: #0d001a !important; }

    /* ── Contenedor principal ── */
    #foryou-wrap {
        height: calc(100vh - 130px);
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 0 8px;
        background: linear-gradient(160deg, #0d001a 0%, #1a0035 60%, #2e0049 100%);
        position: relative;
        overflow: hidden;
        margin: 0;
    }

    /* ── Fondo difuminado dinámico ── */
    #fy-bg {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        filter: blur(40px) brightness(0.35) saturate(1.6);
        transform: scale(1.1);
        transition: background-image 0.8s ease;
        z-index: 0;
    }

    /* ── Tarjeta vertical ── */
    #fy-card {
        position: relative;
        z-index: 10;
        flex-shrink: 1;
        width: min(400px, 90vw);
        /* Asegurar que la tarjeta nunca sea más alta que el espacio disponible */
        max-height: calc(100vh - 200px);
        overflow-y: auto;
        overflow-x: hidden;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(208,150,255,0.2);
        border-radius: 28px;
        backdrop-filter: blur(20px);
        overflow: hidden;
        box-shadow:
            0 0 0 1px rgba(81,18,129,0.4),
            0 30px 80px rgba(0,0,0,0.7),
            inset 0 1px 0 rgba(255,255,255,0.08);
        cursor: grab;
        user-select: none;
        touch-action: none;
        will-change: transform;
        transition: transform 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    #fy-card:active { cursor: grabbing; }

    /* portada cuadrada */
    #fy-cover-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 1/1;
        overflow: hidden;
    }
    #fy-cover {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.6s ease;
    }
    #fy-card:hover #fy-cover { transform: scale(1.03); }

    /* Gradiente inferior sobrepuesto en la portada */
    #fy-cover-wrap::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 55%;
        background: linear-gradient(to top, rgba(13,0,26,0.95) 0%, transparent 100%);
        pointer-events: none;
    }

    /* ── Overlay de Pausa/Play al pasar el cursor ── */
    #fy-hover-control {
        position: absolute;
        inset: 0;
        z-index: 25;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(13,0,26,0);
        transition: background 0.25s ease;
        cursor: pointer;
        /* El icono empieza invisible */
    }
    #fy-hover-control:hover {
        background: rgba(13,0,26,0.45);
    }
    #fy-hover-control .fy-hc-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: rgba(81,18,129,0.85);
        box-shadow: 0 0 30px rgba(124,58,237,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transform: scale(0.8);
        transition: opacity 0.2s ease, transform 0.2s ease;
        pointer-events: none;
        border: 2px solid rgba(192,132,252,0.4);
        backdrop-filter: blur(4px);
    }
    #fy-hover-control:hover .fy-hc-icon {
        opacity: 1;
        transform: scale(1);
    }
    /* El icono SOLO aparece al hacer hover, nunca persiste al salir el cursor */

    /* Indicadores de swipe */
    .fy-swipe-indicator {
        position: absolute;
        top: 50%;
        transform: translateY(-50%) scale(0.7);
        opacity: 0;
        transition: opacity 0.15s, transform 0.15s;
        pointer-events: none;
        z-index: 30;
        border-radius: 50%;
        width: 72px;
        height: 72px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #fy-like-ind  { right: 20px; background: rgba(81,18,129,0.9); box-shadow: 0 0 30px rgba(108,0,180,0.8); }
    #fy-nope-ind  { left: 20px;  background: rgba(120,0,30,0.9); box-shadow: 0 0 30px rgba(200,0,60,0.8); }

    /* Info debajo de la portada */
    #fy-info {
        padding: 16px 20px 0;
        text-align: left;
    }
    #fy-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #fff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        letter-spacing: -.3px;
    }
    #fy-artist {
        font-size: 0.95rem;
        color: #c084fc;
        margin-top: 3px;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #fy-album {
        font-size: 0.78rem;
        color: rgba(208,150,255,0.55);
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ── Barra de progreso ── */
    #fy-progress-wrap {
        margin: 14px 20px 0;
        height: 4px;
        background: rgba(255,255,255,0.12);
        border-radius: 99px;
        overflow: hidden;
    }
    #fy-progress-bar {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #7c3aed, #c084fc);
        border-radius: 99px;
        transition: width 0.5s linear;
    }

    /* ── Fila de tiempo ── */
    #fy-times {
        display: flex;
        justify-content: space-between;
        padding: 5px 20px 0;
        font-size: 0.72rem;
        color: rgba(208,150,255,0.6);
        font-variant-numeric: tabular-nums;
    }

    /* ── Control de volumen ── */
    #fy-volume-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 20px;
    }
    #fy-vol-icon {
        color: rgba(208,150,255,0.7);
        font-size: 1rem;
        flex-shrink: 0;
        cursor: pointer;
        transition: color 0.2s;
    }
    #fy-vol-icon:hover { color: #c084fc; }

    #fy-vol-slider {
        flex: 1;
        -webkit-appearance: none;
        appearance: none;
        height: 4px;
        border-radius: 99px;
        background: rgba(255,255,255,0.15);
        outline: none;
        cursor: pointer;
    }
    #fy-vol-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #a855f7;
        box-shadow: 0 0 8px rgba(168,85,247,0.6);
        cursor: pointer;
        transition: transform 0.15s;
    }
    #fy-vol-slider::-webkit-slider-thumb:hover { transform: scale(1.3); }
    #fy-vol-slider::-moz-range-thumb {
        width: 14px; height: 14px;
        border-radius: 50%;
        background: #a855f7;
        border: none;
        cursor: pointer;
    }

    /* ── Botones de acción ── */
    #fy-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 28px;
        padding: 14px 20px 22px;
    }
    .fy-btn {
        border: none;
        cursor: pointer;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s, box-shadow 0.2s;
        outline: none;
    }
    .fy-btn:active { transform: scale(0.88) !important; }

    #btn-nope {
        width: 58px; height: 58px;
        background: rgba(180, 0, 50, 0.15);
        border: 2px solid rgba(220, 0, 60, 0.4);
        color: #f87171;
    }
    #btn-nope:hover {
        background: rgba(200, 0, 50, 0.25);
        border-color: #f87171;
        transform: scale(1.08);
        box-shadow: 0 0 20px rgba(248,113,113,0.3);
    }

    #btn-like {
        width: 72px; height: 72px;
        background: linear-gradient(135deg, #511281, #7c3aed);
        border: 2px solid rgba(168,85,247,0.6);
        color: #fff;
        box-shadow: 0 8px 24px rgba(81,18,129,0.5);
    }
    #btn-like:hover {
        transform: scale(1.1);
        box-shadow: 0 12px 36px rgba(124,58,237,0.7);
    }

    #btn-skip {
        width: 46px; height: 46px;
        background: rgba(255,255,255,0.05);
        border: 2px solid rgba(208,150,255,0.2);
        color: rgba(208,150,255,0.7);
    }
    #btn-skip:hover {
        border-color: rgba(208,150,255,0.5);
        color: #c084fc;
        transform: scale(1.08);
    }

    /* ── Overlay de play (primera interacción) ── */
    #fy-play-overlay {
        position: absolute;
        inset: 0;
        background: rgba(13,0,26,0.65);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 40;
        backdrop-filter: blur(4px);
        transition: opacity 0.3s;
    }
    #fy-play-overlay.hidden { display: none; }
    .fy-play-btn-big {
        width: 80px; height: 80px;
        background: linear-gradient(135deg, #511281, #7c3aed);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 40px rgba(124,58,237,0.7);
        animation: pulse-purple 2s infinite;
    }
    @keyframes pulse-purple {
        0%,100% { box-shadow: 0 0 30px rgba(124,58,237,0.5); }
        50%      { box-shadow: 0 0 60px rgba(124,58,237,0.9); }
    }

    /* ── Estados loading / error ── */
    #fy-loading, #fy-error {
        position: absolute;
        inset: 0;
        z-index: 50;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
    }
    #fy-loading { display: flex; }
    #fy-error   { display: none; }

    .fy-spinner {
        width: 52px; height: 52px;
        border: 3px solid rgba(168,85,247,0.2);
        border-top-color: #a855f7;
        border-left-color: #c084fc;
        border-radius: 50%;
        animation: spin 0.9s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .fy-loading-text {
        color: rgba(208,150,255,0.8);
        font-size: 0.88rem;
        text-align: center;
        line-height: 1.6;
        font-family: 'Roboto', sans-serif;
    }

    #fy-error-msg  { color: #f87171; font-weight: 700; font-size: 1rem; text-align: center; }
    #fy-retry-btn {
        padding: 10px 28px;
        background: linear-gradient(135deg, #511281, #7c3aed);
        color: #fff;
        border: none;
        border-radius: 99px;
        font-weight: 700;
        cursor: pointer;
        font-family: 'Roboto', sans-serif;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    #fy-retry-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 24px rgba(124,58,237,0.5);
    }

    /* "Para Ti" badge — ahora en el flujo normal, siempre encima de la tarjeta */
    #fy-badge {
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        pointer-events: none;
        z-index: 20;
    }
    #fy-badge h1 {
        font-family: 'Roboto', sans-serif;
        font-size: 1.1rem;
        font-weight: 900;
        color: transparent;
        background: linear-gradient(90deg, #c084fc, #a855f7, #7c3aed);
        -webkit-background-clip: text;
        background-clip: text;
        letter-spacing: 2px;
        text-transform: uppercase;
        white-space: nowrap;
    }
    #fy-badge p {
        font-size: 0.62rem;
        color: rgba(208,150,255,0.6);
        letter-spacing: 3px;
        text-transform: uppercase;
        margin-top: 2px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div id="foryou-wrap">
    <!-- Fondo dinámico -->
    <div id="fy-bg"></div>

    <!-- Badge superior -->
    <div id="fy-badge">
        <h1>Para Ti</h1>
        <p>Explora & Desliza</p>
    </div>

    <!-- Estado: cargando -->
    <div id="fy-loading">
        <div class="fy-spinner"></div>
        <p class="fy-loading-text">Sintonizando tu vibra…<br>Cargando música</p>
    </div>

    <!-- Estado: error -->
    <div id="fy-error">
        <p id="fy-error-msg">Nos quedamos sin ritmos</p>
        <button id="fy-retry-btn" onclick="window.location.reload()">Reintentar</button>
    </div>

    <!-- Tarjeta principal -->
    <div id="fy-card" class="hidden">
        <!-- Portada -->
        <div id="fy-cover-wrap">
            <img id="fy-cover" src="" alt="Portada" draggable="false">

            <!-- Indicadores swipe -->
            <div class="fy-swipe-indicator" id="fy-like-ind">
                <svg width="32" height="32" fill="white" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </div>
            <div class="fy-swipe-indicator" id="fy-nope-ind">
                <svg width="32" height="32" fill="white" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12" stroke="white" stroke-width="3" stroke-linecap="round"/></svg>
            </div>

            <!-- Overlay: primera interacción para audio -->
            <div id="fy-play-overlay">
                <div class="fy-play-btn-big">
                    <svg width="36" height="36" fill="white" viewBox="0 0 24 24"><path d="M5 3l14 9-14 9V3z"/></svg>
                </div>
            </div>

            <!-- Overlay de Pausa/Play al hacer hover -->
            <div id="fy-hover-control">
                <div class="fy-hc-icon" id="fy-hc-icon">
                    <!-- Icono cambia entre pausa y play via JS -->
                    <svg id="fy-hc-pause-svg" width="28" height="28" fill="white" viewBox="0 0 24 24">
                        <rect x="6" y="4" width="4" height="16" rx="1"/>
                        <rect x="14" y="4" width="4" height="16" rx="1"/>
                    </svg>
                    <svg id="fy-hc-play-svg" width="28" height="28" fill="white" viewBox="0 0 24 24" style="display:none;margin-left:3px">
                        <path d="M5 3l14 9-14 9V3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Info -->
        <div id="fy-info">
            <div id="fy-title">—</div>
            <div id="fy-artist">—</div>
            <div id="fy-album">—</div>
        </div>

        <!-- Progreso -->
        <div id="fy-progress-wrap">
            <div id="fy-progress-bar"></div>
        </div>
        <div id="fy-times">
            <span id="fy-elapsed">0:00</span>
            <span id="fy-remaining">0:30</span>
        </div>

        <!-- Volumen -->
        <div id="fy-volume-row">
            <i class="fa-solid fa-volume-low" id="fy-vol-icon"></i>
            <input type="range" id="fy-vol-slider" min="0" max="1" step="0.02" value="0.8">
        </div>

        <!-- Botones -->
        <div id="fy-actions">
            <button class="fy-btn" id="btn-nope" title="No me gusta">
                <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
            <button class="fy-btn" id="btn-like" title="Me gusta">
                <svg width="30" height="30" fill="white" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </button>
            <button class="fy-btn" id="btn-skip" title="Siguiente">
                <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M5.51 3.12A.5.5 0 0 0 5 3.5v17a.5.5 0 0 0 .77.42L18 13.42a.5.5 0 0 0 0-.84L5.77 3.12zM19 3h2v18h-2z"/></svg>
            </button>
        </div>
    </div>

    <audio id="fy-audio"></audio>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ── Elementos UI ── */
    const el = {
        bg: document.getElementById('fy-bg'),
        card: document.getElementById('fy-card'),
        loading: document.getElementById('fy-loading'),
        error: document.getElementById('fy-error'),
        cover: document.getElementById('fy-cover'),
        title: document.getElementById('fy-title'),
        artist: document.getElementById('fy-artist'),
        album: document.getElementById('fy-album'),
        progressBar: document.getElementById('fy-progress-bar'),
        elapsed: document.getElementById('fy-elapsed'),
        remaining: document.getElementById('fy-remaining'),
        volSlider: document.getElementById('fy-vol-slider'),
        volIcon: document.getElementById('fy-vol-icon'),
        playOverlay: document.getElementById('fy-play-overlay'),
        hoverControl: document.getElementById('fy-hover-control'),
        hcPauseSvg: document.getElementById('fy-hc-pause-svg'),
        hcPlaySvg: document.getElementById('fy-hc-play-svg'),
        likeInd: document.getElementById('fy-like-ind'),
        nopeInd: document.getElementById('fy-nope-ind'),
        audio: document.getElementById('fy-audio'),
        btnNope: document.getElementById('btn-nope'),
        btnLike: document.getElementById('btn-like'),
        btnSkip: document.getElementById('btn-skip'),
    };

    /* ── Estado ── */
    let currentTrack = null;
    let queue = [];
    let isPreloading = false;
    let isAnimating = false;
    let isDragging = false;
    let startX = 0;
    let offsetX = 0;
    let audioUnlocked = false;
    const SWIPE_THRESHOLD = 110;

    /* ── Helpers de tiempo ── */
    const fmt = (s) => `${Math.floor(s/60)}:${String(Math.floor(s%60)).padStart(2,'0')}`;

    /* ── Volumen ── */
    el.audio.volume = parseFloat(el.volSlider.value);
    el.volSlider.addEventListener('input', () => {
        el.audio.volume = parseFloat(el.volSlider.value);
        updateVolIcon();
    });
    el.volIcon.addEventListener('click', () => {
        el.audio.muted = !el.audio.muted;
        updateVolIcon();
    });
    function updateVolIcon() {
        const v = el.audio.volume;
        const muted = el.audio.muted;
        el.volIcon.className = muted || v === 0
            ? 'fa-solid fa-volume-xmark'
            : v < 0.4 ? 'fa-solid fa-volume-low'
            : 'fa-solid fa-volume-high';
    }

    /* ── Audio: progreso ── */
    el.audio.addEventListener('timeupdate', () => {
        if (!el.audio.duration) return;
        const pct = el.audio.currentTime / el.audio.duration;
        el.progressBar.style.width = (pct * 100) + '%';
        el.elapsed.textContent = fmt(el.audio.currentTime);
        el.remaining.textContent = fmt(el.audio.duration - el.audio.currentTime);
    });
    el.audio.addEventListener('ended', () => commitSwipe('like', window.innerWidth));

    /* ── Pre-carga de canciones ── */
    async function prefetch() {
        if (isPreloading || queue.length >= 3) return;
        isPreloading = true;
        try {
            const excludeIds = [currentTrack?.id, ...queue.map(t => t.id)].filter(Boolean);
            const res = await fetch('/api/for-you/next?exclude=' + excludeIds.join(','));
            const track = await res.json();
            if (track && track.id && !track.error) {
                queue.push(track);
                setTimeout(prefetch, 400);
            }
        } catch(e) { console.error(e); }
        finally { isPreloading = false; }
    }

    async function init() {
        showState('loading');
        await prefetch();
        if (queue.length > 0) {
            playNext();
        } else {
            showState('error');
        }
    }

    function playNext() {
        if (queue.length === 0) {
            showState('loading');
            prefetch().then(() => queue.length ? playNext() : showState('error'));
            return;
        }
        currentTrack = queue.shift();
        renderTrack(currentTrack);
        prefetch();
    }

    function renderTrack(track) {
        showState('card');

        const img = track.album?.images?.[0]?.url || '';
        el.cover.src = img;
        el.bg.style.backgroundImage = img ? `url('${img}')` : '';
        el.title.textContent = track.name || '—';
        el.artist.textContent = (track.artists || []).map(a => a.name).join(', ') || '—';
        el.album.textContent = track.album?.name || '';

        // Reset UI
        el.progressBar.style.transition = 'none';
        el.progressBar.style.width = '0%';
        el.elapsed.textContent = '0:00';
        el.remaining.textContent = '0:30';
        el.card.style.transition = 'none';
        el.card.style.transform = 'translateX(0) rotate(0deg)';
        el.likeInd.style.opacity = 0;
        el.nopeInd.style.opacity = 0;

        // Audio
        el.audio.pause();
        el.audio.src = track.preview_url || '';
        el.audio.load();

        if (track.preview_url) {
            const p = el.audio.play();
            if (p !== undefined) {
                p.then(() => {
                    el.playOverlay.classList.add('hidden');
                    audioUnlocked = true;
                    setTimeout(() => {
                        el.progressBar.style.transition = 'width 0.5s linear';
                    }, 100);
                }).catch(() => {
                    el.playOverlay.classList.remove('hidden');
                });
            }
        }

        isAnimating = false;
    }

    /* ── Swipe ── */
    function handleStart(e) {
        if (isAnimating) return;
        isDragging = true;
        startX = e.type.startsWith('mouse') ? e.clientX : e.touches[0].clientX;
        el.card.style.transition = 'none';
    }

    function handleMove(e) {
        if (!isDragging) return;
        if (e.type.startsWith('touch')) e.preventDefault();
        const x = e.type.startsWith('mouse') ? e.clientX : e.touches[0].clientX;
        offsetX = x - startX;
        el.card.style.transform = `translateX(${offsetX}px) rotate(${offsetX/18}deg)`;
        const norm = Math.min(Math.abs(offsetX) / SWIPE_THRESHOLD, 1);
        el.likeInd.style.opacity = offsetX > 0 ? norm : 0;
        el.likeInd.style.transform = `translateY(-50%) scale(${0.7 + 0.3*norm})`;
        el.nopeInd.style.opacity = offsetX < 0 ? norm : 0;
        el.nopeInd.style.transform = `translateY(-50%) scale(${0.7 + 0.3*norm})`;
    }

    function handleEnd() {
        if (!isDragging) return;
        isDragging = false;
        el.card.style.transition = 'transform 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        if (offsetX > SWIPE_THRESHOLD) {
            commitSwipe('like', window.innerWidth * 1.5);
        } else if (offsetX < -SWIPE_THRESHOLD) {
            commitSwipe('dislike', -window.innerWidth * 1.5);
        } else {
            el.card.style.transform = 'translateX(0) rotate(0deg)';
            el.likeInd.style.opacity = 0;
            el.nopeInd.style.opacity = 0;
        }
    }

    function commitSwipe(action, endX) {
        if (isAnimating) return;
        isAnimating = true;
        audioUnlocked = true;
        el.audio.pause();
        el.card.style.transition = 'transform 0.45s ease-out, opacity 0.3s ease';
        el.card.style.transform = `translateX(${endX}px) rotate(${endX/14}deg)`;
        el.card.style.opacity = '0';
        sendAction(action);
        setTimeout(() => {
            el.card.style.opacity = '1';
            playNext();
        }, 380);
    }

    async function sendAction(action) {
        if (!currentTrack) return;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        try {
            fetch('/api/for-you/action', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    track_id: currentTrack.id,
                    album_id: currentTrack.album?.id,
                    action
                })
            });
        } catch(e) {}
    }

    /* ── Botones ── */
    el.btnLike.addEventListener('click', () => commitSwipe('like', window.innerWidth * 1.5));
    el.btnNope.addEventListener('click', () => commitSwipe('dislike', -window.innerWidth * 1.5));
    el.btnSkip.addEventListener('click', () => commitSwipe('like', window.innerWidth * 1.5));

    /* ── Hover Control: clic en la portada = pausa/reanuda ── */
    // Solo actualiza qué icono se muestra (pausa o play)
    // La VISIBILIDAD del icono la controla solo el CSS :hover
    function updateHoverControlIcon(paused) {
        el.hcPauseSvg.style.display = paused ? 'none' : 'block';
        el.hcPlaySvg.style.display  = paused ? 'block' : 'none';
        // Sin is-paused: el icono desaparece cuando el cursor sale, siempre
    }

    el.hoverControl.addEventListener('click', (e) => {
        e.stopPropagation();
        if (!audioUnlocked) {
            audioUnlocked = true;
            el.audio.play()
                .then(() => { el.playOverlay.classList.add('hidden'); updateHoverControlIcon(false); })
                .catch(() => {});
            return;
        }
        if (el.audio.paused) {
            el.audio.play().then(() => updateHoverControlIcon(false)).catch(() => {});
        } else {
            el.audio.pause();
            updateHoverControlIcon(true);
        }
    });

    /* ── Eventos táctiles y ratón ── */
    el.card.addEventListener('mousedown', handleStart);
    window.addEventListener('mousemove', handleMove, { passive: false });
    window.addEventListener('mouseup', handleEnd);
    el.card.addEventListener('touchstart', handleStart, { passive: false });
    window.addEventListener('touchmove', handleMove, { passive: false });
    window.addEventListener('touchend', handleEnd);

    /* ── Desbloquear audio en primer clic ── */
    document.body.addEventListener('click', () => {
        if (!audioUnlocked && currentTrack?.preview_url) {
            audioUnlocked = true;
            el.audio.play().then(() => el.playOverlay.classList.add('hidden')).catch(() => {});
        }
    }, { once: false });

    /* ── Toggle estados ── */
    function showState(state) {
        el.loading.style.display = state === 'loading' ? 'flex' : 'none';
        el.error.style.display   = state === 'error'   ? 'flex' : 'none';
        el.card.classList.toggle('hidden', state !== 'card');
    }

    /* ── Arranque ── */
    init();
    // Asegurar que la altura del contenedor no incluya el footer
    document.getElementById('foryou-wrap').style.height =
        (window.innerHeight - (document.getElementById('foryou-wrap').offsetTop)) + 'px';
    window.addEventListener('resize', () => {
        document.getElementById('foryou-wrap').style.height =
            (window.innerHeight - (document.getElementById('foryou-wrap').offsetTop)) + 'px';
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RecicledApp\resources\views/discovery/foryou.blade.php ENDPATH**/ ?>