// Búsqueda en vivo con debounce
(function () {
    const campoBusqueda = document.getElementById('campo-busqueda');
    const resultadosBusqueda = document.getElementById('resultados-busqueda');

    if (!campoBusqueda || !resultadosBusqueda) return;

    let timeoutId = null;

    // Función debounce
    function debounce(func, delay) {
        return function (...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Función para realizar la búsqueda
    async function realizarBusqueda(termino) {
        // Limpiar si el término está vacío
        if (!termino || termino.trim().length < 2) {
            resultadosBusqueda.innerHTML = '';
            resultadosBusqueda.classList.add('oculto');
            return;
        }

        try {
            const url = `${window.searchRoute}?termino=${encodeURIComponent(termino)}`;
            const response = await fetch(url);
            const resultados = await response.json();

            // Limpiar resultados anteriores
            resultadosBusqueda.innerHTML = '';

            if (resultados.length === 0) {
                resultadosBusqueda.innerHTML = '<div style="padding: 15px; text-align: center; color: #999;">No se encontraron resultados</div>';
                resultadosBusqueda.classList.remove('oculto');
                return;
            }

            // Crear elementos para cada resultado
            resultados.forEach(resultado => {
                const item = document.createElement('a');
                item.href = resultado.url;
                item.className = 'resultado-item';

                const imagen = document.createElement('img');
                imagen.src = resultado.imagen;
                imagen.alt = resultado.nombre;
                imagen.className = 'resultado-imagen';
                imagen.onerror = function () {
                    this.src = 'multimedia/img/default-artist.jpg';
                };

                const info = document.createElement('div');
                info.className = 'resultado-info';

                const nombre = document.createElement('div');
                nombre.className = 'resultado-nombre';
                nombre.textContent = resultado.nombre;

                const tipo = document.createElement('div');
                tipo.className = 'resultado-tipo';
                tipo.textContent = resultado.tipo === 'artista' ? 'Artista' : 'Canción';

                info.appendChild(nombre);
                info.appendChild(tipo);

                // Si es canción, añadir nombre del artista
                if (resultado.tipo === 'cancion' && resultado.artista) {
                    const artista = document.createElement('div');
                    artista.className = 'resultado-artista';
                    artista.textContent = resultado.artista;
                    info.appendChild(artista);
                }

                item.appendChild(imagen);
                item.appendChild(info);
                resultadosBusqueda.appendChild(item);
            });

            // Mostrar resultados
            resultadosBusqueda.classList.remove('oculto');

        } catch (error) {
            console.error('Error en la búsqueda:', error);
            resultadosBusqueda.innerHTML = '<div style="padding: 15px; text-align: center; color: #f00;">Error al buscar</div>';
            resultadosBusqueda.classList.remove('oculto');
        }
    }

    // Event listener con debounce
    campoBusqueda.addEventListener('input', debounce(function (e) {
        realizarBusqueda(e.target.value);
    }, 300));

    // Ocultar resultados al hacer clic fuera
    document.addEventListener('click', function (e) {
        if (!campoBusqueda.contains(e.target) && !resultadosBusqueda.contains(e.target)) {
            resultadosBusqueda.classList.add('oculto');
        }
    });

    // Mostrar resultados al hacer clic en el campo si ya hay contenido
    campoBusqueda.addEventListener('focus', function () {
        if (resultadosBusqueda.children.length > 0) {
            resultadosBusqueda.classList.remove('oculto');
        }
    });
})();
