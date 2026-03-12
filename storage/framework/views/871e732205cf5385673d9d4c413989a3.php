

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/estilos-home.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<main>
    <div class="superior">
        <div class="info-artista">
            <h3>Título</h3>
            <p class="nombre-artista">Nombre artista</p>
            <p class="info-contexto">Contexto de canción, biografía, información...</p>
        </div>
        <div class="novedades">
            <h3 class="titulo-novedades">Novedades</h3>
            <div class="carrusel" id="carrusel">
                <div class="elemento-carrusel left" data-index="0">
                    <!-- Album: Lux (Real ID) -->
                    <a href="<?php echo e(route('album.show', '3SUEJULSGgBDG1j4GQhfYY')); ?>">
                        <img src="<?php echo e(asset('multimedia/img/Portadas/album/rosalia-lux.webp')); ?>" alt="Lux de Rosalia">
                    </a>
                </div>
                <div class="elemento-carrusel center" data-index="1">
                    <!-- Album: CHROMAKOPIA (Real ID) -->
                    <a href="<?php echo e(route('album.show', '0U28P0QVB1QRxpqp5IHOlH')); ?>">
                        <img src="<?php echo e(asset('multimedia/img/Portadas/album/cromakopia - Tyler the creator.png')); ?>" alt="Chromakopia">
                    </a>
                </div>
                <div class="elemento-carrusel right" data-index="2">
                    <!-- Album: Debi Tirar Mas Fotos (Real ID) -->
                    <a href="<?php echo e(route('album.show', '5K79FLRUCSysQnVESLcTdb')); ?>">
                        <img src="<?php echo e(asset('multimedia/img/Portadas/album/dtmf - bad bunny.png')); ?>" alt="Debi Tirar Mas Fotos">
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mid">
        <div class="encabezado-popular">
            <h2 class="popular">Lo más escuchado del mes</h2>
            <div class="filtro">
                <select>
                    <option selected>Selecciona Género</option>
                    <option value="rock">Rock</option>
                    <option value="pop">Pop</option>
                    <option value="electronica">Electrónica</option>
                </select>
            </div>
        </div>

        <div class="cuadricula-canciones">
            <div class="elementos">
                <span class="numero-cancion">1</span>
                <img src="<?php echo e(asset('multimedia/img/Portadas/album/rosalia-lux.webp')); ?>" alt="Canción 1">
                <p class="titulo-cancion">
                    <a href="#" class="enlace-discreto">Berghain</a>
                </p>
                <p class="artista">
                    <a href="<?php echo e(route('artista.show', '7ltDVBr6mKbRvohxheJ9h1')); ?>" class="enlace-discreto">ROSALÍA</a>, Björk & Yves Tumor
                </p>
                <div class="visualizaciones">
                    <i class="fa-solid fa-eye"></i>
                    <p>260.9K</p>
                </div>
            </div>

            <div class="elementos">
                <span class="numero-cancion">2</span>
                <img src="<?php echo e(asset('multimedia/img/Portadas/album/rosalia-lux.webp')); ?>" alt="Canción 2">
                <p class="titulo-cancion">
                    <a href="#" class="enlace-discreto">La Perla</a>
                </p>
                <p class="artista">
                    <a href="<?php echo e(route('artista.show', '7ltDVBr6mKbRvohxheJ9h1')); ?>" class="enlace-discreto">ROSALÍA</a> & Yahritza Y Su Esencia
                </p>
                <div class="visualizaciones">
                    <i class="fa-solid fa-eye"></i>
                    <p>143.8K</p>
                </div>
            </div>

            <div class="elementos">
                <span class="numero-cancion">3</span>
                <img src="<?php echo e(asset('multimedia/img/Portadas/album/rosalia-lux.webp')); ?>" alt="Canción 3">
                <p class="titulo-cancion">
                    <a href="#" class="enlace-discreto">La Yugular</a>
                </p>
                <p class="artista">
                    <a href="<?php echo e(route('artista.show', '7ltDVBr6mKbRvohxheJ9h1')); ?>" class="enlace-discreto">ROSALÍA</a>
                </p>
                <div class="visualizaciones">
                    <i class="fa-solid fa-eye"></i>
                    <p>118.7K</p>
                </div>
            </div>

            <div class="elementos">
                <span class="numero-cancion">4</span>
                <img src="<?php echo e(asset('multimedia/img/Portadas/album/dtmf - bad bunny.png')); ?>" alt="Canción 4">
                <p class="titulo-cancion">
                    <a href="#" class="enlace-discreto">NUEVAYoL</a>
                </p>
                <p class="artista">
                    <a href="<?php echo e(route('artista.show', '4q3ewBCX7sLwd24euuV69X')); ?>" class="enlace-discreto">Bad Bunny</a>
                </p>
                <div class="visualizaciones">
                    <i class="fa-solid fa-eye"></i>
                    <p>386.1K</p>
                </div>
            </div>
        </div>
    </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/logica-home.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RecicledApp\resources\views/home.blade.php ENDPATH**/ ?>