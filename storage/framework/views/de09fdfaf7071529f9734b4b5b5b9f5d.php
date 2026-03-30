<?php $__env->startSection('title', 'Mis Me Gustas'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    body { background: #f8f5ff !important; }
    main { padding: 0 !important; background: #f8f5ff !important; }

    /* ── Página ── */
    .likes-page {
        min-height: calc(100vh - 130px);
        background: #f8f5ff;
        padding: 40px 5% 60px;
        font-family: 'Roboto', sans-serif;
    }

    /* ── Cabecera ── */
    .likes-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 48px;
    }
    .likes-icon {
        width: 54px; height: 54px;
        background: #f0e6ff;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        border: 1.5px solid #d8b4fe;
        flex-shrink: 0;
    }
    .likes-icon i { color: #7c3aed; font-size: 1.4rem; }
    .likes-header h1 {
        font-size: 2rem;
        font-weight: 900;
        color: #3b0764;
        letter-spacing: -0.5px;
    }
    .likes-header p {
        color: #9333ea;
        font-size: 0.88rem;
        margin-top: 2px;
        opacity: 0.7;
    }

    /* ── Sección ── */
    .likes-section { margin-bottom: 52px; }

    .likes-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.78rem;
        font-weight: 800;
        color: #7c3aed;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 2px solid #ede9fe;
    }
    .likes-section-title i { font-size: 1rem; color: #a855f7; }
    .count-badge {
        background: #ede9fe;
        color: #7c3aed;
        padding: 2px 10px;
        border-radius: 99px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0;
        border: 1px solid #ddd6fe;
    }

    /* ── Carrusel ── */
    .carousel-wrap {
        position: relative;
    }
    .carousel-track {
        display: flex;
        gap: 16px;
        overflow-x: auto;
        scroll-behavior: smooth;
        padding-bottom: 8px;
        scrollbar-width: none;         /* Firefox */
    }
    .carousel-track::-webkit-scrollbar { display: none; } /* Chrome */

    /* Flechas */
    .carousel-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        width: 40px; height: 40px;
        border-radius: 50%;
        border: 1.5px solid #ddd6fe;
        background: #fff;
        color: #7c3aed;
        font-size: 1rem;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(124,58,237,0.12);
        transition: background 0.2s, border-color 0.2s, transform 0.2s;
    }
    .carousel-arrow:hover {
        background: #7c3aed;
        color: #fff;
        border-color: #7c3aed;
    }
    .carousel-arrow-left  { left: -20px; }
    .carousel-arrow-right { right: -20px; }
    .carousel-arrow.hidden { opacity: 0; pointer-events: none; }

    /* ── Cards ── */
    .like-card {
        position: relative;
        flex-shrink: 0;
        width: 168px;
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        border: 1.5px solid #ede9fe;
        box-shadow: 0 2px 12px rgba(124,58,237,0.07);
        transition: transform 0.22s, box-shadow 0.22s, border-color 0.22s;
        cursor: default;
    }
    .like-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(124,58,237,0.18);
        border-color: #c4b5fd;
    }

    /* Portada */
    .like-card-cover {
        position: relative;
        width: 100%;
        aspect-ratio: 1/1;
        overflow: hidden;
        background: #f0e6ff;
    }
    .like-card-cover img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.35s;
    }
    .like-card:hover .like-card-cover img { transform: scale(1.05); }

    /* Overlay oscuro sutil al hover */
    .like-card-cover::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(59,7,100,0);
        transition: background 0.22s;
    }
    .like-card:hover .like-card-cover::after {
        background: rgba(59,7,100,0.15);
    }

    /* Botón X — aparece en hover */
    .like-card-remove {
        position: absolute;
        top: 8px; right: 8px;
        z-index: 20;
        width: 28px; height: 28px;
        border-radius: 50%;
        background: rgba(255,255,255,0.92);
        border: 1.5px solid #f0e6ff;
        color: #7c3aed;
        font-size: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        opacity: 0;
        transform: scale(0.8);
        transition: opacity 0.18s, transform 0.18s, background 0.15s, color 0.15s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .like-card:hover .like-card-remove {
        opacity: 1;
        transform: scale(1);
    }
    .like-card-remove:hover {
        background: #f87171;
        color: #fff;
        border-color: #f87171;
    }

    /* Mini play button (canciones) */
    .like-card-play {
        position: absolute;
        bottom: 10px; right: 10px;
        z-index: 20;
        width: 34px; height: 34px;
        border-radius: 50%;
        background: #7c3aed;
        border: none;
        color: #fff;
        font-size: 0.8rem;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        opacity: 0;
        transform: translateY(4px);
        transition: opacity 0.18s, transform 0.18s, background 0.15s;
        box-shadow: 0 4px 12px rgba(124,58,237,0.4);
    }
    .like-card:hover .like-card-play { opacity: 1; transform: translateY(0); }
    .like-card-play:hover { background: #6d28d9; }
    .like-card-play.playing {
        opacity: 1;
        transform: translateY(0);
        animation: pulse-ring 1.5s infinite;
    }
    @keyframes pulse-ring {
        0%,100% { box-shadow: 0 0 0 0 rgba(124,58,237,0.3); }
        50%      { box-shadow: 0 0 0 8px rgba(124,58,237,0); }
    }

    /* Cuerpo de la card */
    .like-card-body {
        padding: 10px 12px 14px;
        background: #fff;
    }
    .like-card-name {
        font-size: 0.88rem;
        font-weight: 700;
        color: #2d1060;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.3;
    }
    .like-card-sub {
        font-size: 0.74rem;
        color: #9333ea;
        margin-top: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        opacity: 0.75;
    }

    /* Artista: foto circular */
    .artist-cover img {
        border-radius: 50%;
        width: calc(100% - 28px);
        height: calc(100% - 28px);
        margin: 14px;
        object-fit: cover;
    }
    .artist-cover::after { display: none; }

    /* ── Estado vacío ── */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 40px 24px;
        background: #fff;
        border-radius: 20px;
        border: 1.5px dashed #ddd6fe;
        color: #c4b5fd;
        width: 100%;
    }
    .empty-state i { font-size: 2.2rem; }
    .empty-state p { font-size: 0.88rem; color: #a78bfa; text-align: center; margin: 0; }
    .empty-state a {
        margin-top: 4px;
        color: #7c3aed;
        font-weight: 700;
        font-size: 0.85rem;
        text-decoration: none;
    }
    .empty-state a:hover { text-decoration: underline; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="likes-page">

    <!-- ── Cabecera ── -->
    <div class="likes-header">
        <div class="likes-icon">
            <i class="fa-solid fa-heart"></i>
        </div>
        <div>
            <h1>Mis Me Gustas</h1>
            <p><?php echo e($songs->count() + $albums->count() + $artists->count()); ?> elementos guardados</p>
        </div>
    </div>

    <!-- ══════════ CANCIONES ══════════ -->
    <div class="likes-section">
        <div class="likes-section-title">
            <i class="fa-solid fa-music"></i>
            Canciones
            <span class="count-badge"><?php echo e($songs->count()); ?></span>
        </div>

        <?php if($songs->isEmpty()): ?>
            <div class="carousel-track">
                <div class="empty-state">
                    <i class="fa-solid fa-music"></i>
                    <p>Todavía no has guardado ninguna canción.</p>
                    <a href="<?php echo e(route('foryou')); ?>">¡Desliza en Para Ti →</a>
                </div>
            </div>
        <?php else: ?>
            <div class="carousel-wrap" data-carousel="songs">
                <button class="carousel-arrow carousel-arrow-left hidden" onclick="scrollCarousel('songs', -1)">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="carousel-track" id="track-songs" onscroll="updateArrows('songs')">
                    <?php $__currentLoopData = $songs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $song): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="like-card">
                        <div class="like-card-cover">
                            <img src="<?php echo e($song->image_url ?: ''); ?>"
                                 alt="<?php echo e($song->name); ?>"
                                 onerror="this.src='https://placehold.co/168x168/ede9fe/7c3aed?text=♪'">

                            <!-- X para eliminar -->
                            <form method="POST" action="<?php echo e(route('likes.destroy', $song->id)); ?>" style="display:contents">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="like-card-remove" title="Quitar me gusta">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>

                            <!-- Play preview -->
                            <?php if($song->extra['preview_url'] ?? null): ?>
                            <button class="like-card-play"
                                    onclick="togglePreview(this, '<?php echo e($song->extra['preview_url']); ?>')"
                                    title="Escuchar preview">
                                <i class="fa-solid fa-play"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                        <div class="like-card-body">
                            <div class="like-card-name"><?php echo e($song->name); ?></div>
                            <div class="like-card-sub"><?php echo e($song->artist_name ?? '—'); ?></div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <button class="carousel-arrow carousel-arrow-right" onclick="scrollCarousel('songs', 1)">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- ══════════ ÁLBUMES ══════════ -->
    <div class="likes-section">
        <div class="likes-section-title">
            <i class="fa-solid fa-record-vinyl"></i>
            Álbumes
            <span class="count-badge"><?php echo e($albums->count()); ?></span>
        </div>

        <?php if($albums->isEmpty()): ?>
            <div class="carousel-track">
                <div class="empty-state">
                    <i class="fa-solid fa-record-vinyl"></i>
                    <p>Aún no has guardado ningún álbum.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="carousel-wrap" data-carousel="albums">
                <button class="carousel-arrow carousel-arrow-left hidden" onclick="scrollCarousel('albums', -1)">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="carousel-track" id="track-albums" onscroll="updateArrows('albums')">
                    <?php $__currentLoopData = $albums; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $album): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="like-card">
                        <div class="like-card-cover">
                            <img src="<?php echo e($album->image_url ?: ''); ?>"
                                 alt="<?php echo e($album->name); ?>"
                                 onerror="this.src='https://placehold.co/168x168/ede9fe/7c3aed?text=LP'">
                            <form method="POST" action="<?php echo e(route('likes.destroy', $album->id)); ?>" style="display:contents">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="like-card-remove" title="Quitar me gusta">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>
                        <div class="like-card-body">
                            <div class="like-card-name"><?php echo e($album->name); ?></div>
                            <div class="like-card-sub"><?php echo e($album->artist_name ?? ''); ?></div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <button class="carousel-arrow carousel-arrow-right" onclick="scrollCarousel('albums', 1)">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- ══════════ ARTISTAS ══════════ -->
    <div class="likes-section">
        <div class="likes-section-title">
            <i class="fa-solid fa-star"></i>
            Artistas
            <span class="count-badge"><?php echo e($artists->count()); ?></span>
        </div>

        <?php if($artists->isEmpty()): ?>
            <div class="carousel-track">
                <div class="empty-state">
                    <i class="fa-solid fa-star"></i>
                    <p>Todavía no sigues a ningún artista.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="carousel-wrap" data-carousel="artists">
                <button class="carousel-arrow carousel-arrow-left hidden" onclick="scrollCarousel('artists', -1)">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="carousel-track" id="track-artists" onscroll="updateArrows('artists')">
                    <?php $__currentLoopData = $artists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $artist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="like-card">
                        <div class="like-card-cover artist-cover">
                            <img src="<?php echo e($artist->image_url ?: ''); ?>"
                                 alt="<?php echo e($artist->name); ?>"
                                 onerror="this.src='https://placehold.co/168x168/ede9fe/7c3aed?text=🎤'">
                            <form method="POST" action="<?php echo e(route('likes.destroy', $artist->id)); ?>" style="display:contents">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="like-card-remove" title="Quitar me gusta">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>
                        <div class="like-card-body">
                            <div class="like-card-name"><?php echo e($artist->name); ?></div>
                            <div class="like-card-sub">Artista</div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <button class="carousel-arrow carousel-arrow-right" onclick="scrollCarousel('artists', 1)">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>

</div>

<audio id="preview-audio" style="display:none;"></audio>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
/* ── Carrusel ── */
const SCROLL_AMOUNT = 380;

function scrollCarousel(id, dir) {
    const track = document.getElementById('track-' + id);
    track.scrollLeft += dir * SCROLL_AMOUNT;
}

function updateArrows(id) {
    const track = document.getElementById('track-' + id);
    const wrap  = track.closest('.carousel-wrap');
    const left  = wrap.querySelector('.carousel-arrow-left');
    const right = wrap.querySelector('.carousel-arrow-right');
    left.classList.toggle('hidden',  track.scrollLeft <= 4);
    right.classList.toggle('hidden', track.scrollLeft + track.clientWidth >= track.scrollWidth - 4);
}

// Inicializar flechas al cargar
document.querySelectorAll('.carousel-track').forEach(track => {
    const id = track.id?.replace('track-', '');
    if (id) updateArrows(id);
});

/* ── Preview audio ── */
let currentPlayBtn = null;
const previewAudio = document.getElementById('preview-audio');

function togglePreview(btn, url) {
    if (currentPlayBtn && currentPlayBtn !== btn) {
        previewAudio.pause();
        currentPlayBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
        currentPlayBtn.classList.remove('playing');
    }
    if (previewAudio.src.includes(encodeURIComponent(url).slice(0,20)) && !previewAudio.paused) {
        previewAudio.pause();
        btn.innerHTML = '<i class="fa-solid fa-play"></i>';
        btn.classList.remove('playing');
        currentPlayBtn = null;
    } else {
        previewAudio.src = url;
        previewAudio.play().then(() => {
            btn.innerHTML = '<i class="fa-solid fa-pause"></i>';
            btn.classList.add('playing');
            currentPlayBtn = btn;
        }).catch(() => {});
    }
}

previewAudio.addEventListener('ended', () => {
    if (currentPlayBtn) {
        currentPlayBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
        currentPlayBtn.classList.remove('playing');
        currentPlayBtn = null;
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RecicledApp\resources\views/likes/index.blade.php ENDPATH**/ ?>