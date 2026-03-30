<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/styleLogin.css')); ?>?v=1.3">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=lock" />
    <style>
    .material-symbols-outlined {
      font-variation-settings:
      'FILL' 0,
      'wght' 400,
      'GRAD' 0,
      'opsz' 24
    }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-wrapper">
    <h1>Crea tu cuenta</h1>
    <p style="text-align: center; margin-bottom: 20px; color: var(--dark-gray);">Únete a nuestra comunidad de música.</p>

    <div class="contenedor-autenticacion">
        <form action="<?php echo e(route('register')); ?>" method="POST" onsubmit="return validarRegistro()">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="tipo_usuario" value="usuario">

            <label for="nombre">Nombre de usuario</label>
            <div class="grupo-entrada">
                <i class="fa-regular fa-user icono-izquierda"></i>
                <input type="text" id="nombre" name="nombre_usuario" placeholder="Introduce tu nombre de usuario" value="<?php echo e(old('nombre_usuario')); ?>" required autofocus>
            </div>
            <?php $__errorArgs = ['nombre_usuario'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-red-500 text-sm block mt-1 mb-2"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <label for="email">Correo electrónico</label>
            <div class="grupo-entrada">
                <i class="fa-regular fa-envelope icono-izquierda"></i>
                <input type="email" id="email" name="email" placeholder="Introduce tu correo electrónico" value="<?php echo e(old('email')); ?>" required>
            </div>
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-red-500 text-sm block mt-1 mb-2"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <label for="password">Contraseña</label>
            <div class="grupo-entrada">
                <span class="material-symbols-outlined icono-izquierda">lock</span>
                <div class="contenedor-input-icono">
                    <input type="password" id="password" name="password" placeholder="Introduce tu contraseña" required>
                    <i class="fa-solid fa-eye alternar-contrasena" style="cursor: pointer;"></i>
                </div>
            </div>
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-red-500 text-sm block mt-1 mb-2"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <label for="password_confirmation">Confirmar contraseña</label>
            <div class="grupo-entrada">
                <span class="material-symbols-outlined icono-izquierda">lock</span>
                <div class="contenedor-input-icono">
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirma tu contraseña" required>
                    <i class="fa-solid fa-eye alternar-contrasena" style="cursor: pointer;"></i>
                </div>
            </div>

            <button type="submit" class="boton-enviar">Registrarse</button>
            
            <div style="text-align: center; margin-top: 15px;">
                <span style="color: var(--dark-gray); font-size: 0.9em;">¿Eres un artista?</span>
                <!-- TODO: Crear ruta de registro de artista -->
                <a href="<?php echo e(route('register.artist')); ?>" style="color: var(--primary-purple); font-weight: bold; font-size: 0.9em; text-decoration: none;">Crea tu cuenta de artista aquí</a>
            </div>
        </form>
        
        <p style="font-size: 0.8em; text-align: center; margin-top: 15px; color: var(--dark-gray);">
            Al registrarte, aceptas nuestra <a href="#" style="color: var(--primary-purple);">Política de Privacidad</a> y <a href="#" style="color: var(--primary-purple);">Términos de Servicio</a>.
        </p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... (other scripts if any, but toggle logic is removed)
    });

    function validarRegistro() {
        const pass = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirmation').value;
        if (pass !== confirm) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Las contraseñas no coinciden',
                confirmButtonColor: '#d33'
            });
            return false;
        }
        return true;
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RecicledApp\resources\views/auth/register.blade.php ENDPATH**/ ?>