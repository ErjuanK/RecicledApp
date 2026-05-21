

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/estilo-cancion.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<main class="contenedor-cancion">
    
    <div class="diseño-desglose">
        <aside class="panel-informacion">
            <div class="portada-cancion-grande">
                <img src="<?php echo e($cancion->portada_url); ?>" alt="<?php echo e($cancion->titulo); ?>">
            </div>
            
            <div class="info-metadatos">
                <h1 class="titulo-metadatos"><?php echo e($cancion->titulo); ?></h1>
                <h2 class="artista-metadatos">
                    <a href="<?php echo e(route('artista.show', $cancion->artista_id)); ?>" style="text-decoration: none; color: inherit;">
                        <?php echo e($cancion->artista); ?>

                    </a>
                </h2>
                <div class="detalles-metadatos">
                    <p>
                        <i class="fa-solid fa-record-vinyl"></i> 
                        <a href="<?php echo e(route('album.show', $cancion->album_id)); ?>" style="text-decoration: none; color: inherit;">
                            <?php echo e($cancion->album); ?>

                        </a>
                    </p>
                    <p><i class="fa-regular fa-clock"></i> <?php echo e($cancion->duracion); ?></p>
                </div>

                <div class="acciones-cancion" style="margin-top: 20px;">
                    <?php
                        $isLikedSong = isset($likedSongs) && in_array($cancion->id, $likedSongs);
                    ?>
                    <button class="boton-icono" onclick="toggleLike(this, 'song', '<?php echo e($cancion->id); ?>', '<?php echo e(addslashes($cancion->titulo)); ?>', '<?php echo e(addslashes($cancion->artista)); ?>', '<?php echo e($cancion->portada_url); ?>', '')" style="background:none; border:none; font-size: 1.8rem; cursor:pointer; padding:0; transition: transform 0.2s;">
                        <i class="fa-solid fa-heart" style="<?php echo e($isLikedSong ? 'color: #a855f7;' : 'color: #d1d5db;'); ?>"></i>
                    </button>
                </div>
            </div>

            <div class="creditos-metadatos">
                <h3>Créditos</h3>
                <ul class="lista-creditos-simple">
                    <?php $__currentLoopData = $cancion->creditos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $credito): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <span class="rol"><?php echo e($credito['rol']); ?></span>
                        <span class="nombre"><?php echo e($credito['nombres']); ?></span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </aside>

        <!-- Panel Derecho: Letra Scrolleable -->
        <section class="panel-letra" id="lyrics-panel">
            <h3 class="encabezado-letra-minimal">Letra</h3>
            
            <div class="contenido-letra-limpio position-relative" id="lyrics-content">
                <?php if(!empty($cancion->letra_html)): ?>
                    <div class="letra-real">
                        <?php echo $cancion->letra_html; ?>

                    </div>
                <?php else: ?>
                    <?php $__currentLoopData = $cancion->letra_simulada; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parrafo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p><?php echo e($parrafo); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
            
            </div>
        </section>

    </div>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RecicledApp\resources\views/cancion.blade.php ENDPATH**/ ?>