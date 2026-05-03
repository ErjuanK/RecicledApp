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
                        <span class="release-type"><?php echo e(strtoupper($album['album_type'] ?? 'ALBUM')); ?></span>
                        <h3 class="release-title"><?php echo e($album['name']); ?></h3>
                        <p class="release-artist"><?php echo e($album['artists'][0]['name'] ?? 'Artista Desconocido'); ?></p>
                    </div>
                </a>
            </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <hr class="newspaper-divider">
    </section>
    <?php endif; ?>

    <section class="global-section">
        <div class="section-heading">
            <h2>NOVEDADES MUNDIALES</h2>
            <span>Lo último que está sonando en el mundo.</span>
        </div>
        
        <?php if(!empty($globalReleases)): ?>
            <div class="newspaper-layout">
                
                <?php $mainRelease = array_shift($globalReleases); ?>
                <?php if($mainRelease): ?>
                <article class="release-card main-feature">
                    <a href="<?php echo e($mainRelease['external_urls']['spotify'] ?? '#'); ?>" target="_blank" rel="noopener noreferrer">
                        <div class="release-image-wrapper">
                            <?php if(!empty($mainRelease['images'][0]['url'])): ?>
                                <img src="<?php echo e($mainRelease['images'][0]['url']); ?>" alt="<?php echo e($mainRelease['name']); ?>">
                            <?php else: ?>
                                <div class="placeholder-img"><i class="fa-solid fa-compact-disc"></i></div>
                            <?php endif; ?>
                            <div class="badge-exclusive">EN PORTADA</div>
                        </div>
                        <div class="release-info">
                            <span class="release-type"><?php echo e(strtoupper($mainRelease['album_type'] ?? 'ALBUM')); ?></span>
                            <h3 class="release-title headline"><?php echo e($mainRelease['name']); ?></h3>
                            <p class="release-artist"><?php echo e($mainRelease['artists'][0]['name'] ?? 'Artista Desconocido'); ?></p>
                            <p class="release-description">El nuevo y esperado trabajo de <?php echo e($mainRelease['artists'][0]['name'] ?? 'Artista Desconocido'); ?> ya está disponible. Una obra que promete revolucionar las listas de éxitos.</p>
                        </div>
                    </a>
                </article>
                <?php endif; ?>

                
                <div class="secondary-features">
                    <?php $__currentLoopData = array_slice($globalReleases, 0, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $album): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="release-card secondary-card">
                        <a href="<?php echo e($album['external_urls']['spotify'] ?? '#'); ?>" target="_blank" rel="noopener noreferrer">
                            <div class="release-info">
                                <span class="release-type"><?php echo e(strtoupper($album['album_type'] ?? 'ALBUM')); ?></span>
                                <h3 class="release-title"><?php echo e($album['name']); ?></h3>
                                <p class="release-artist"><?php echo e($album['artists'][0]['name'] ?? 'Artista Desconocido'); ?></p>
                            </div>
                            <div class="release-image-wrapper mini-img">
                                <?php if(!empty($album['images'][0]['url'])): ?>
                                    <img src="<?php echo e($album['images'][0]['url']); ?>" alt="<?php echo e($album['name']); ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="placeholder-img"><i class="fa-solid fa-compact-disc"></i></div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            
            <hr class="newspaper-divider-light">
            
            
            <div class="releases-grid">
                <?php $__currentLoopData = array_slice($globalReleases, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $album): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="release-card normal-card">
                    <a href="<?php echo e($album['external_urls']['spotify'] ?? '#'); ?>" target="_blank" rel="noopener noreferrer">
                        <div class="release-image-wrapper">
                            <?php if(!empty($album['images'][0]['url'])): ?>
                                <img src="<?php echo e($album['images'][0]['url']); ?>" alt="<?php echo e($album['name']); ?>" loading="lazy">
                            <?php else: ?>
                                <div class="placeholder-img"><i class="fa-solid fa-compact-disc"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="release-info">
                            <span class="release-type"><?php echo e(strtoupper($album['album_type'] ?? 'ALBUM')); ?></span>
                            <h3 class="release-title"><?php echo e($album['name']); ?></h3>
                            <p class="release-artist"><?php echo e($album['artists'][0]['name'] ?? 'Artista Desconocido'); ?></p>
                        </div>
                    </a>
                </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; padding: 50px; color: #a1a1aa;">No se encontraron novedades en este momento.</p>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RecicledApp\resources\views/releases/index.blade.php ENDPATH**/ ?>