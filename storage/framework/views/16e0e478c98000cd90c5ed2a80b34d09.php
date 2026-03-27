

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/estilo-artista.css')); ?>">
    <style>
        /* Remove default main padding for full-width hero */
        main { padding: 0 !important; }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="contenedor-hero-artista" style="width: 100vw; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw;">
    <div class="imagen-hero" style="background-image: url('<?php echo e($artista->imagen_hero); ?>');">
        <div class="superposicion-hero"></div>
    </div>
</div>

<div class="contenedor-principal-artista">
    
    <aside class="barra-lateral-artista">
        <div class="contenedor-imagen-perfil">
            <img src="<?php echo e($artista->imagen_perfil); ?>" alt="<?php echo e($artista->nombre_artistico); ?>" class="imagen-perfil">
        </div>
        
        <h1 class="nombre-artista"><?php echo e($artista->nombre_artistico); ?></h1>
        
        <div class="seccion-sobre-artista">
            <h3 class="titulo-sobre">Biografía</h3>
            
            <div class="texto-biografia-artista">
                <?php
                $biografia = $artista->biografia;
                $limite = 200;
                ?>
                
                <?php if(strlen($biografia) > $limite): ?>
                    <?php
                    $corte = strpos($biografia, ' ', $limite);
                    if ($corte === false) $corte = $limite;
                    $visible = substr($biografia, 0, $corte);
                    $resto = substr($biografia, $corte);
                    ?>
                    <span class="biografia-visible"><?php echo nl2br(e($visible)); ?>...</span>
                    <span class="biografia-oculta" style="display:none;"><?php echo nl2br(e($resto)); ?></span>
                    <a href="#" class="enlace-leer-mas" onclick="toggleBiografia(event)">seguir leyendo</a>
                <?php else: ?>
                    <?php echo nl2br(e($biografia)); ?>

                <?php endif; ?>
            </div>
            
            <?php if(!empty($artista->generos)): ?>
            <div class="generos-artista">
                <?php $__currentLoopData = array_slice($artista->generos, 0, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genero): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="etiqueta-genero"><?php echo e(ucfirst($genero)); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
    </aside>
    
    <main class="contenido-artista">
        
        <section class="seccion-contenido">
            <h2 class="titulo-seccion" style="color: #6F00D0; font-size: 1.5em;">CANCIONES POPULARES DE <?php echo e(strtoupper($artista->nombre_artistico)); ?></h2>
            
            <div class="cuadricula-canciones">
                <?php $__currentLoopData = array_slice($artista->canciones_populares, 0, 8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cancion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('cancion.show', $cancion->id)); ?>" class="tarjeta-cancion">
                    <div class="imagen-tarjeta-cancion">
                        <img src="<?php echo e($cancion->miniatura_url); ?>" alt="Cover">
                    </div>
                    <div class="info-tarjeta-cancion">
                        <h3 class="titulo-tarjeta-cancion"><?php echo e($cancion->titulo); ?></h3>
                        <p class="artista-tarjeta-cancion"><?php echo e($cancion->artista_nombre); ?></p>
                        <div class="vistas-tarjeta-cancion">
                            <i class="fa-regular fa-eye"></i> <?php echo e($cancion->vistas_formateadas); ?>

                        </div>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
        
        <section class="seccion-contenido">
            <div class="contenedor-busqueda-completo">
                <i class="fa-solid fa-magnifying-glass icono-busqueda"></i>
                <input type="text" id="buscador-canciones-artista" placeholder="Ver todas las canciones de <?php echo e($artista->nombre_artistico); ?>" style="color: #6F00D0;">
            </div>
            <div id="resultados-busqueda-artista" class="resultados-busqueda"></div>
        </section>
        
        <section class="seccion-contenido">
            <h2 class="titulo-seccion" style="color: #6F00D0; font-size: 1.5em;">ÁLBUMES</h2>
            <div class="cuadricula-albumes-creativa">
                <?php $__currentLoopData = $artista->albumes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $album): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('album.show', $album->id)); ?>" class="elemento-album item-<?php echo e(($index % 6) + 1); ?>">
                        <div class="envoltura-portada-album">
                            <img src="<?php echo e($album->portada_url); ?>" alt="<?php echo e($album->nombre_album); ?>">
                            <div class="info-superpuesta-album">
                                <span class="anio-album"><?php echo e($album->anio); ?></span>
                            </div>
                        </div>
                        <div class="titulo-album-simple"><?php echo e($album->nombre_album); ?></div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
        
    </main>
</div>

<script type="application/json" id="datos-canciones-artista">
<?php echo json_encode($artista->todas_las_canciones); ?>

</script>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/busqueda-artista.js')); ?>"></script>
<script>
function toggleBiografia(event) {
    event.preventDefault();
    const bioOculta = document.querySelector('.biografia-oculta');
    const enlace = event.target;
    
    if (bioOculta.style.display === 'none') {
        bioOculta.style.display = 'inline';
        enlace.textContent = 'mostrar menos';
    } else {
        bioOculta.style.display = 'none';
        enlace.textContent = 'seguir leyendo';
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RecicledApp\resources\views/artista.blade.php ENDPATH**/ ?>