<?php $__env->startSection('title', 'Lanzamientos de la Semana'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/releases.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="releases-container">
        <header class="releases-header">
            <h1 class="newspaper-title">EL ECO MUSICAL</h1>
            <div class="newspaper-date"><?php echo e(\Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y')); ?></div>
            <p class="newspaper-subtitle">Los lanzamientos más candentes de la semana, directamente a tus oídos.</p>
            <hr class="newspaper-divider">
        </header>

        <?php if(Auth::check() && !empty($personalizedReleases)): ?>
            <section class="personalized-section">
                <div class="section-heading">
                    <h2>PARA TI</h2>
                    <span>Basado en tus artistas favoritos: <?php echo e(implode(', ', array_slice($likedArtistsNames, 0, 3))); ?><?php echo e(count($likedArtistsNames) > 3 ? '...' : ''); ?></span>
                </div>
                <div class="releases-grid personalized-grid">
                    <?php $__currentLoopData = $personalizedReleases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $album): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="release-card highlight-card">
                            <a href="<?php echo e($album['external_urls']['spotify'] ?? '#'); ?>" target="_blank" rel="noopener noreferrer">
                                <div class="release-image-wrapper">
                                    <?php if(!empty($album['images'][0]['url'])): ?>
                                        <img src="<?php echo e($album['images'][0]['url']); ?>" alt="<?php echo e($album['name']); ?>" loading="lazy">
                                    <?php else: ?>
                                        <div class="placeholder-img"><i class="fa-solid fa-compact-disc"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div class="release-info">
                                    <?php if(!empty($album['reason'])): ?>
                                        <span class="release-reason"><i class="fa-solid fa-wand-magic-sparkles"></i> <?php echo e($album['reason']); ?></span>
                                    <?php endif; ?>
                                    <span class="release-type"><?php echo e(strtoupper($album['album_type'] ?? 'ALBUM')); ?> &bull;
                                        <?php echo e(\Carbon\Carbon::parse($album['release_date'] ?? now())->translatedFormat('d M, Y')); ?></span>
                                    <h3 class="release-title"><?php echo e($album['name']); ?></h3>
                                    <p class="release-artist"><?php echo e($album['artists'][0]['name'] ?? 'Artista Desconocido'); ?></p>
                                    <p class="release-meta" style="font-size: 0.8rem; color: #71717a; margin-top: 5px;">
                                        <?php echo e($album['total_tracks'] ?? 1); ?> pistas
                                    </p>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if(!empty($recentEditorialReleases)): ?>
            <section class="magazine-editorial-section">
                <div class="section-heading">
                    <h2>NOTICIAS Y DESTACADOS</h2>
                </div>

                <div class="magazine-layout">
                    <?php $heroArticle = $recentEditorialReleases[0] ?? null; ?>
                    <?php if($heroArticle): ?>
                    <!-- Artículo Principal (Izquierda) -->
                    <article class="magazine-hero">
                        <?php
                            $heroUrl = route('album.itunes', [
                                'artist' => urlencode($heroArticle['itunes_artist'] ?? $heroArticle['artist']),
                                'album'  => urlencode($heroArticle['itunes_album']  ?? $heroArticle['title']),
                            ]);
                            $heroTooltip = 'www.recicled.app/proyectos/' . Str::slug($heroArticle['artist']) . '/' . Str::slug($heroArticle['title']);
                        ?>
                        <a href="<?php echo e($heroUrl); ?>" class="magazine-hero-image-wrapper tooltip-container" style="display: block; text-decoration: none;">
                            <?php if(!empty($heroArticle['cover_url'])): ?>
                                <img src="<?php echo e($heroArticle['cover_url']); ?>" alt="Portada <?php echo e($heroArticle['title']); ?>" class="magazine-hero-img">
                            <?php else: ?>
                                <div class="magazine-hero-img" style="background:#2d1b69; display:flex; align-items:center; justify-content:center;">
                                    <i class="fa-solid fa-compact-disc" style="font-size:5rem; color:#c084fc;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="tooltip-content">
                                <i class="fa-solid fa-link"></i> <?php echo e($heroTooltip); ?>

                            </div>
                        </a>
                        <div class="magazine-hero-text">
                            <span class="magazine-tag">RESEÑA • <?php echo e(strtoupper($heroArticle['type'])); ?></span>
                            <h2 class="magazine-headline"><?php echo e($heroArticle['description']); ?></h2>
                            <p class="magazine-description">El nuevo proyecto <strong>"<?php echo e($heroArticle['title']); ?>"</strong> de <strong><?php echo e($heroArticle['artist']); ?></strong> ya está dando de qué hablar.</p>
                            <div class="magazine-meta">por Redacción / <?php echo e($heroArticle['date']); ?></div>
                            <div class="magazine-cta-container">
                                <a href="<?php echo e($heroUrl); ?>" class="btn-internal-link">
                                    [VER PÁGINA DEL PROYECTO COMPLETO Y DISCOGRAFÍA]
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endif; ?>

                    <!-- Últimas Noticias (Derecha) -->
                    <aside class="magazine-sidebar">
                        <div class="sidebar-header">
                            <h3 class="sidebar-title">Últimas Noticias</h3>
                            <hr class="sidebar-divider">
                        </div>
                        <div class="sidebar-list">
                            <?php $__currentLoopData = array_slice($recentEditorialReleases, 1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $reviewUrl = route('album.itunes', [
                                        'artist' => urlencode($review['itunes_artist'] ?? $review['artist']),
                                        'album'  => urlencode($review['itunes_album']  ?? $review['title']),
                                    ]);
                                ?>
                                <a href="<?php echo e($reviewUrl); ?>" class="sidebar-card">
                                    <div class="sidebar-card-image">
                                        <?php if(!empty($review['cover_url'])): ?>
                                            <img src="<?php echo e($review['cover_url']); ?>" alt="Portada <?php echo e($review['title']); ?>" loading="lazy">
                                        <?php else: ?>
                                            <div style="width:100%;height:100%;background:#2d1b69;display:flex;align-items:center;justify-content:center;">
                                                <i class="fa-solid fa-compact-disc" style="color:#c084fc;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="sidebar-card-text">
                                        <span class="sidebar-tag"><?php echo e(strtoupper($review['type'])); ?></span>
                                        <h4><?php echo e($review['title']); ?> — <?php echo e($review['artist']); ?></h4>
                                        <div class="sidebar-meta"><?php echo e($review['date']); ?></div>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </aside>
                </div>
            </section>
        <?php endif; ?>



        <?php if(!empty($upcomingReleases)): ?>
            <section class="upcoming-section">
                <div class="section-heading">
                    <h2>EN EL RADAR: PRÓXIMOS LANZAMIENTOS</h2>
                </div>

                <div class="upcoming-list">
                    <?php $__currentLoopData = $upcomingReleases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upcoming): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="upcoming-card">
                            <div class="upcoming-date-badge">
                                <span class="upcoming-day"><i class="fa-regular fa-calendar"></i></span>
                                <span class="upcoming-month"><?php echo e($upcoming['date']); ?></span>
                            </div>
                            <div class="upcoming-content">
                                <h3 class="upcoming-title"><?php echo e($upcoming['title']); ?></h3>
                                <p class="upcoming-artist"><?php echo e($upcoming['artist']); ?> <span class="upcoming-type">&bull;
                                        <?php echo e($upcoming['type']); ?></span></p>
                                <p class="upcoming-description"><?php echo e($upcoming['description']); ?></p>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RecicledApp\resources\views/releases/index.blade.php ENDPATH**/ ?>