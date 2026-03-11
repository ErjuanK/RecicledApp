/**
 * Búsqueda de Artista (Optimización JSON Client-Side)
 * Lee los datos inyectados en el DOM para filtrar sin peticiones al servidor.
 */

function toggleBiografia(e) {
    e.preventDefault();
    const bioOculta = document.querySelector('.biografia-oculta');
    const link = document.querySelector('.enlace-leer-mas');
    const bioVisible = document.querySelector('.biografia-visible');

    if (bioOculta && link) {
        if (bioOculta.style.display === 'none') {
            bioOculta.style.display = 'inline';
            link.textContent = '« leer menos';
            if (bioVisible) bioVisible.style.display = 'inline'; // Ensure visible part stays
        } else {
            bioOculta.style.display = 'none';
            link.textContent = 'seguir leyendo';
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // 1. Cargar datos del script JSON
    const dataScript = document.getElementById('datos-canciones-artista');
    let allSongs = [];

    if (dataScript) {
        try {
            allSongs = JSON.parse(dataScript.textContent);
            console.log("Canciones cargadas para búsqueda:", allSongs.length);
        } catch (e) {
            console.error("Error parseando canciones:", e);
        }
    }

    const input = document.getElementById('buscador-canciones-artista');
    const container = document.getElementById('resultados-busqueda-artista');

    if (input && container) {
        input.addEventListener('input', function (e) {
            const query = e.target.value.toLowerCase().trim();
            container.innerHTML = '';

            if (query.length < 1) return;

            // 2. Filtrar en memoria (rápido)
            const matches = allSongs.filter(song =>
                song.titulo.toLowerCase().includes(query)
            );

            // 3. Renderizar resultados
            if (matches.length > 0) {
                matches.slice(0, 10).forEach(song => { // Límite visual de 10
                    const item = document.createElement('a');
                    item.href = `index.php?action=cancion&id=${song.id}`; // Enlace real
                    item.className = 'tarjeta-cancion'; // Reuse styled card
                    // Override styles for list view if needed, or stick to cards
                    item.style.marginBottom = '5px';

                    item.innerHTML = `
                        <div class="imagen-tarjeta-cancion" style="width: 40px; height: 40px; margin-right: 10px;">
                            <img src="${song.miniatura_url}" alt="Cover">
                        </div>
                        <div class="info-tarjeta-cancion">
                            <h3 class="titulo-tarjeta-cancion" style="font-size: 0.9rem;">${song.titulo}</h3>
                            <p class="artista-tarjeta-cancion" style="font-size: 0.8rem;">${song.album_nombre}</p>
                        </div>
                    `;
                    container.appendChild(item);
                });
            } else {
                container.innerHTML = '<div style="padding: 10px; color: #999;">No se encontraron resultados.</div>';
            }
        });
    }
});
