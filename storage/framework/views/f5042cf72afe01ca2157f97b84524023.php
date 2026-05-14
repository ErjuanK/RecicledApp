

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/estilo-album.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<main class="contenedor-album">
    <div class="banner-cabecera-album">
        <h1 class="titulo-banner-album"><?php echo e($album->nombre); ?></h1>
    </div>

    <div class="cuadricula-contenido-album">
        <!-- Columna Izquierda: Portada e Info -->
        <aside class="barra-lateral-album">
            <div class="contenedor-portada-album">
                <img src="<?php echo e($album->portada_url); ?>" alt="<?php echo e($album->nombre); ?>" class="img-portada-album">
            </div>
            
            <div class="detalles-album">
                <h2 class="nombre-album"><?php echo e($album->nombre); ?></h2>
                <h3 class="artista-album">
                    <?php if($album->artista_id): ?>
                        <a href="<?php echo e(route('artista.show', $album->artista_id)); ?>" style="text-decoration: none; color: inherit;">
                            <?php echo e($album->artista); ?>

                        </a>
                    <?php else: ?>
                        <?php echo e($album->artista); ?>

                    <?php endif; ?>
                </h3>
                
                <p class="descripcion-album">
                    <?php echo e($album->descripcion); ?>

                </p>

                <div class="acciones-album">
                    <button class="boton-icono"><i class="fa-solid fa-heart"></i></button>
                    <button class="boton-icono"><i class="fa-solid fa-share"></i></button>
                </div>
            </div>
        </aside>

        <!-- Columna Derecha: Lista de Canciones -->
        <section class="canciones-album">
            <div class="cabecera-canciones">
                <span class="col-titulo">Lista de Canciones</span>
                <span class="col-encabezado-titulo">TÍTULO</span>
                <span class="col-reloj"><i class="fa-regular fa-clock"></i></span>
            </div>

            <div class="lista-canciones">
                <?php $__currentLoopData = $album->canciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $cancion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $cancionIsLocal = is_numeric($cancion->id); ?>
                <?php if($cancionIsLocal): ?>
                <a href="<?php echo e(route('cancion.show', $cancion->id)); ?>" class="elemento-cancion" style="text-decoration:none; color:inherit;">
                <?php else: ?>
                <div class="elemento-cancion">
                <?php endif; ?>
                    <span class="numero-cancion-lista"><?php echo e($index + 1); ?></span>
                    <div class="info-cancion-lista">
                        <span class="titulo-cancion-lista"><?php echo e($cancion->titulo); ?></span>
                        <span class="artista-cancion-lista"><?php echo e($cancion->artista); ?></span>
                    </div>
                    <span class="duracion-cancion-lista"><?php echo e($cancion->duracion); ?></span>
                    <span class="opciones-cancion"><i class="fa-solid fa-ellipsis-vertical"></i></span>
                <?php if($cancionIsLocal): ?>
                </a>
                <?php else: ?>
                </div>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    </div>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RecicledApp\resources\views/album.blade.php ENDPATH**/ ?>