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
                    <span>Basado en tus artistas favoritos: <?php echo e(implode(', ', array_slice($likedArtistsNames, 0, 3))); ?>...</span>
                </div>
                <div class="releases-grid personalized-grid">
                    <?php $__currentLoopData = array_slice($personalizedReleases, 0, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $album): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
            <section class="genius-editorial-section">
                <div class="section-heading">
                    <h2>NOTICIAS Y DESTACADOS</h2>
                </div>

                <?php
                    // Extract the first item as the hero feature
                    $featuredEditorial = array_shift($recentEditorialReleases);
                ?>

                <a href="#" class="genius-hero" style="text-decoration: none; color: inherit; display: flex;">
                    <div class="genius-hero-text">
                        <span class="genius-tag">RESEÑA • <?php echo e(strtoupper($featuredEditorial['type'])); ?></span>
                        <h2><?php echo e($featuredEditorial['description']); ?></h2>
                        <p>El nuevo proyecto "<?php echo e($featuredEditorial['title']); ?>" de
                            <strong><?php echo e($featuredEditorial['artist']); ?></strong> ya está dando de qué hablar.
                        </p>
                        <div class="genius-meta">por Redacción / <?php echo e($featuredEditorial['date']); ?></div>
                    </div>
                    <div class="genius-hero-image">
                        <img src="https://picsum.photos/seed/<?php echo e(md5($featuredEditorial['title'])); ?>/800/450"
                            alt="Portada <?php echo e($featuredEditorial['title']); ?>">
                    </div>
                </a>

                <div class="genius-grid">
                    <?php $__currentLoopData = $recentEditorialReleases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="#" class="genius-card" style="text-decoration: none; color: inherit;">
                            <div class="genius-card-text">
                                <span class="genius-tag"><?php echo e(strtoupper($review['type'])); ?></span>
                                <h3><?php echo e($review['description']); ?></h3>
                                <div class="genius-meta">por Redacción / <?php echo e($review['date']); ?></div>
                            </div>
                            <div class="genius-card-image">
                                <img src="https://picsum.photos/seed/<?php echo e(md5($review['title'])); ?>/400/400"
                                    alt="Portada <?php echo e($review['title']); ?>">
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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