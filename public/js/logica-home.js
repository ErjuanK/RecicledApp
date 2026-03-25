/**
 * Lógica específica para la página de inicio
 */
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.carrusel');
    const items = document.querySelectorAll('.elemento-carrusel');
    
    // Rotar si hay más de 1 elemento
    if (container && items.length > 1) {
        setInterval(() => {
            // Añadir animación suave de salida
            const firstItem = container.firstElementChild;
            firstItem.style.opacity = '0';
            firstItem.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                // Moverlo al final
                container.appendChild(firstItem);
                // Restaurar estilos
                firstItem.style.opacity = '1';
                firstItem.style.transform = '';
            }, 400); // 400ms para la transición
        }, 3500); // Rota cada 3.5 segundos
    }
});
