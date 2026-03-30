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
    <h1>Inicia Sesión</h1>

    <div class="contenedor-autenticacion">
        <form action="<?php echo e(route('login')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <label for="email">Correo Electrónico</label>
            <div class="grupo-entrada">
                <i class="fa-regular fa-envelope icono-izquierda"></i>
                <input type="email" id="email" name="email" placeholder="Introduce tu correo electrónico" value="<?php echo e(old('email')); ?>" required autofocus>
            </div>
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span style="color:red; font-size: 0.9em;"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <label for="password">Contraseña</label>
            <div class="grupo-entrada">
                <span class="material-symbols-outlined icono-izquierda">lock</span>
                <div class="contenedor-input-icono">
                    <input type="password" id="password" name="password" placeholder="Introduce tu contraseña" required>
                    <i class="fa-solid fa-eye alternar-contrasena icono-derecha"></i>
                </div>
            </div>
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span style="color:red; font-size: 0.9em;"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <a href="#" class="olvido-contrasena">¿Olvidaste tu contraseña?</a>

            <button type="submit" class="boton-enviar">Iniciar Sesión</button>

            <div class="separador">
                <span>O inicia sesión con</span>
            </div>

            <div class="inicio-social">
                <button type="button" class="boton-social"><i class="fa-brands fa-google google-icon"></i></button>
                <button type="button" class="boton-social"><i class="fa-brands fa-apple"></i></button>
            </div>
        </form>
    </div>

    <p class="enlace-registro">
        ¿Aún no tienes una cuenta? <a href="<?php echo e(route('register')); ?>">Regístrate aquí</a>
    </p>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejo de SweetAlerts desde sesión de Laravel
        <?php if(session('success')): ?>
            const successType = "<?php echo e(session('success')); ?>";
            // Lógica genérica por defecto, se puede personalizar más si el controlador envía 'types' específicos
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: successType,
                confirmButtonColor: '#6F00D0'
            });
        <?php endif; ?>

        <?php if(session('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "<?php echo e(session('error')); ?>",
                confirmButtonColor: '#d33'
            });
        <?php endif; ?>
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RecicledApp\resources\views/auth/login.blade.php ENDPATH**/ ?>