

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/estilos-home.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<main>
    <section class="novedades-showcase">
        <h2 class="novedades-titulo">Nuevos Lanzamientos</h2>
        <div class="novedades-grid">
            <?php $__currentLoopData = array_slice($albums, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $album): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $genres = array_slice($album['genres'] ?? [], 0, 3);
                    $albumName = $album['name'] ?? 'Álbum';
                    $imgUrl = $album['images'][0]['url'] ?? asset('multimedia/img/Portadas/album/default.png');
                    $albumId = $album['id'];
                    $artistName = $album['artists'][0]['name'] ?? 'Varios Artistas';
                ?>
                <a href="<?php echo e(route('album.show', $albumId)); ?>" class="tarjeta-novedad">
                    <img src="<?php echo e($imgUrl); ?>" alt="<?php echo e($albumName); ?>" class="tarjeta-fondo">
                    <div class="tarjeta-overlay">
                        <div class="tarjeta-info">
                            <p class="tarjeta-artista"><?php echo e($artistName); ?></p>
                            <h3 class="tarjeta-nombre"><?php echo e($albumName); ?></h3>
                            <?php if(!empty($genres)): ?>
                                <div class="tarjeta-generos">
                                    <?php $__currentLoopData = $genres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="tarjeta-tag"><?php echo e(ucfirst($g)); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <div class="mid">
        <div class="encabezado-popular">
            <h2 class="popular">Lo más escuchado del mes<?php echo e($genre ? ' (' . ucfirst($genre) . ')' : ' en España'); ?></h2>
            <div class="filtro">
                <select onchange="window.location.href='?genre=' + this.value">
                    <option value="" <?php echo e(empty($genre) ? 'selected' : ''); ?>>Todos (Top 50 España)</option>
                    <option value="rock" <?php echo e($genre == 'rock' ? 'selected' : ''); ?>>Rock</option>
                    <option value="pop" <?php echo e($genre == 'pop' ? 'selected' : ''); ?>>Pop</option>
                    <option value="electronica" <?php echo e($genre == 'electronica' ? 'selected' : ''); ?>>Electrónica</option>
                    <option value="reggaeton" <?php echo e($genre == 'reggaeton' ? 'selected' : ''); ?>>Reggaetón</option>
                    <option value="hip hop" <?php echo e($genre == 'hip hop' ? 'selected' : ''); ?>>Hip Hop</option>
                </select>
            </div>
        </div>

        <div class="cuadricula-canciones">
            <?php $__currentLoopData = $tracks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $track): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="elementos">
                    <span class="numero-cancion"><?php echo e($index + 1); ?></span>
                    <img src="<?php echo e($track['album']['images'][0]['url'] ?? asset('multimedia/img/Portadas/album/default.png')); ?>" alt="<?php echo e($track['name']); ?>">
                    <p class="titulo-cancion">
                        <a href="<?php echo e(route('cancion.show', $track['id'])); ?>" class="enlace-discreto"><?php echo e(current(explode(' (', $track['name']))); ?></a>
                    </p>
                    <p class="artista">
                        <?php $__currentLoopData = $track['artists']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $artist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('artista.show', $artist['id'])); ?>" class="enlace-discreto"><?php echo e($artist['name']); ?></a><?php echo e($i < count($track['artists']) - 1 ? ', ' : ''); ?>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </p>
                    <div class="visualizaciones">
                        <i class="fa-solid fa-play"></i>
                        <p><?php echo e(number_format($track['popularity'] ?? 0 * 1000)); ?></p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/logica-home.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RecicledApp\resources\views/home.blade.php ENDPATH**/ ?>