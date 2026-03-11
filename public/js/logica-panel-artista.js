document.addEventListener('DOMContentLoaded', function () {
    const avatarImg = document.getElementById('avatar-visual');
    const btnCamera = document.getElementById('btn-camera');
    const inputAvatar = document.getElementById('input-avatar');
    const formPerfil = document.getElementById('form-perfil-artista');
    const inputNombre = document.getElementById('input-nombre-artistico');
    const bioHeader = document.getElementById('bio-header');

    // --- A. Lógica Avatar ---
    function updateCameraVisibility() {
        const activeTab = document.querySelector('.pestana.activa');
        const isPerfil = activeTab && activeTab.id === 'tab-perfil';

        if (btnCamera) {
            btnCamera.style.display = isPerfil ? 'flex' : 'none';
        }
    }

    // Inicializar estado del botón cámara
    updateCameraVisibility();

    // Observer para cambios en las pestañas (si cambian por otra vía)
    // O mejor, exponer la función para que el onclick del HTML la llame.
    // Como el onclick está en HTML, escucharemos clicks en la navegación.
    const navPestanas = document.querySelector('.navegacion-pestanas');
    if (navPestanas) {
        navPestanas.addEventListener('click', function (e) {
            // Dar un pequeño delay para que la clase activa cambie
            setTimeout(updateCameraVisibility, 50);
        });
    }

    function handleAvatarClick() {
        const activeTab = document.querySelector('.pestana.activa');
        const isPerfil = activeTab && activeTab.id === 'tab-perfil';

        if (isPerfil) {
            // Caso 1: Pestaña Perfil -> Subir Foto
            inputAvatar.click();
        } else {
            // Caso 2: Otras Pestañas -> Lightbox
            Swal.fire({
                imageUrl: avatarImg.src,
                imageAlt: 'Avatar en grande',
                showConfirmButton: false,
                background: 'transparent',
                backdrop: `rgba(0,0,0,0.8)`,
                customClass: {
                    popup: 'swal2-no-padding' // Clase opcional si quieres quitar padding
                },
                width: 'auto',
                padding: 0
            });
        }
    }

    if (avatarImg) {
        avatarImg.addEventListener('click', handleAvatarClick);
        avatarImg.style.cursor = 'pointer';
    }

    if (btnCamera) {
        btnCamera.addEventListener('click', function (e) {
            e.stopPropagation();
            inputAvatar.click();
        });
    }

    // Previsualizar imagen al seleccionar
    if (inputAvatar) {
        inputAvatar.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    if (avatarImg) avatarImg.src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    if (formPerfil) {
        formPerfil.addEventListener('submit', function (e) {
            e.preventDefault();
            console.log("Intentando enviar formulario...");

            // Verificar cambio de nombre
            const originalName = inputNombre ? inputNombre.getAttribute('data-original') : '';
            const currentName = inputNombre ? inputNombre.value : '';

            if (inputNombre && currentName !== originalName) {
                Swal.fire({
                    title: '¿Cambiar Nombre Artístico?',
                    text: "Vas a cambiar tu Nombre Artístico. Esto afectará a cómo apareces en todos tus álbumes y canciones. ¿Estás seguro?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#6F00D0',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        enviarFormulario();
                    }
                });
            } else {
                enviarFormulario();
            }
        });
    }

    function enviarFormulario() {
        // Asegurar que el form existe
        if (!formPerfil) return;

        const formData = new FormData(formPerfil);

        // Añadir solo si hay archivo nuevo
        if (inputAvatar && inputAvatar.files.length > 0) {
            formData.set('avatar', inputAvatar.files[0]);
        }

        console.log("Enviando datos...", Object.fromEntries(formData));

        fetch('index.php?action=actualizar_perfil_artista', {
            method: 'POST',
            body: formData
        })
            .then(response => response.text()) // Cambiar a text primero para debuggear si falla JSON
            .then(text => {
                console.log("Respuesta raw:", text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error("Respuesta del servidor no válida: " + text);
                }
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cambios guardados',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    if (inputNombre) {
                        inputNombre.setAttribute('data-original', inputNombre.value);
                    }

                    if (bioHeader) {
                        const bioText = formData.get('biografia');
                        actualizarBioHeader(bioText);
                    }

                    // Actualizar titulo principal
                    const mainTitle = document.querySelector('.info-basica-artista h1');
                    if (mainTitle && inputNombre) {
                        mainTitle.textContent = inputNombre.value;
                    }

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudieron guardar los cambios'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error inesperado',
                    text: 'Hubo un problema al conectar con el servidor. Revisa la consola.'
                });
            });
    }

    function actualizarBioHeader(fullText) {
        if (!bioHeader) return;
        if (!fullText) fullText = "";

        const posPunto = fullText.indexOf('.');
        const posSalto = fullText.indexOf('\n');

        let corte = -1;
        if (posPunto !== -1 && posSalto !== -1) {
            corte = Math.min(posPunto, posSalto);
        } else if (posPunto !== -1) {
            corte = posPunto;
        } else if (posSalto !== -1) {
            corte = posSalto;
        }

        let html = '';
        if (corte !== -1) {
            html = fullText.substring(0, corte + 1) + ' <span style="color: #6F00D0; font-weight: bold;">...</span>';
        } else {
            if (fullText.length > 100) {
                html = fullText.substring(0, 100) + ' <span style="color: #6F00D0; font-weight: bold;">...</span>';
            } else {
                html = fullText;
            }
        }
        bioHeader.innerHTML = html;
    }
});
