document.addEventListener('DOMContentLoaded', function () {
    const iconosAlternar = document.querySelectorAll('.alternar-contrasena');

    iconosAlternar.forEach(icono => {
        icono.addEventListener('click', function () {
            // Buscar el input hermano anterior (asumiendo la estructura HTML acordada)
            // Estructura: <div class="contenedor-input-icono"> <input> <i> </div>
            const contenedor = this.closest('.contenedor-input-icono');
            if (contenedor) {
                const input = contenedor.querySelector('input');

                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    }
                }
            }
        });
    });
});
