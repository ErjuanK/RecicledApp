const carousel = document.getElementById('carrusel');
const items = Array.from(document.querySelectorAll('.elemento-carrusel'));
const infoArt = document.querySelector('.info-artista');
const titulo = infoArt.querySelector('h3');
const nomArt = infoArt.querySelector('.nombre-artista');
const contextInfo = infoArt.querySelector('.info-contexto');

let currentIndex = 1;
let autoplayInterval;

// Objeto con la información de cada imagen
const metadata = {
    0: {
        titulo: "LUX",
        artista: "Rosalía",
        contexto: "Un album que fusiona flamenco con ritmos urbanos, explorando temas de deseo y seducción."
    },
    1: {
        titulo: "CHROMAKOPIA",
        artista: "Tyler, the Creator",
        contexto: "El esperado sexto álbum de estudio, que promete una nueva evolución en su sonido y lírica."
    },
    2: {
        titulo: "Debí tirar mas fotos",
        artista: "Bad Bunny",
        contexto: "Un álbum que marca un regreso a las raíces del trap del artista, con un tono más introspectivo y oscuro."
    }
};

function updateCarousel() {
    items.forEach((item, index) => {
        item.classList.remove('left', 'center', 'right');

        const prevIndex = (currentIndex - 1 + items.length) % items.length;
        const nextIndex = (currentIndex + 1) % items.length;

        if (index === prevIndex) {
            item.classList.add('left');
        } else if (index === currentIndex) {
            item.classList.add('center');
            // Actualizar la información cuando la imagen está en el centro
            const currentMetadata = metadata[index];
            if (currentMetadata) {
                titulo.textContent = currentMetadata.titulo;
                nomArt.textContent = currentMetadata.artista;
                contextInfo.textContent = currentMetadata.contexto;
            }
        } else if (index === nextIndex) {
            item.classList.add('right');
        } else {
            // Ocultar elementos que no están en vista
            item.style.opacity = '0';
            item.style.transform = 'translateX(900px) scale(0.5)';
        }
    });
}

function nextSlide() {
    currentIndex = (currentIndex + 1) % items.length;
    updateCarousel();
}

function prevSlide() {
    currentIndex = (currentIndex - 1 + items.length) % items.length;
    updateCarousel();
}

function goToSlide(index) {
    currentIndex = index;
    updateCarousel();
}

function startAutoplay() {
    autoplayInterval = setInterval(nextSlide, 3000);
}

function stopAutoplay() {
    clearInterval(autoplayInterval);
}

// Click en las imágenes para centrarlas
items.forEach((item, index) => {
    item.addEventListener('click', () => {
        if (index !== currentIndex) {
            goToSlide(index);
        }
    });
});

// Llamada inicial para establecer la información correcta al cargar la página
updateCarousel();

// Iniciar autoplay
startAutoplay();