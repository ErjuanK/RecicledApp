@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilo-panel-artista.css') }}">
@endpush

@section('content')
<div class="contenedor-panel">
    
    <!-- Header Artista -->
    <div class="cabecera-artista">
        <div class="contenedor-avatar-relativo" style="position: relative; display: inline-block;">
            <img src="{{ $user->avatar ? asset($user->avatar) : 'https://via.placeholder.com/150' }}" alt="Avatar Artista" class="avatar-artista" id="avatar-visual">
            <button type="button" class="btn-cambiar-avatar" id="btn-camera" style="
                position: absolute;
                bottom: 5px;
                right: 5px;
                width: 35px;
                height: 35px;
                border-radius: 50%;
                background: white;
                border: none;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #6F00D0;
                transition: transform 0.2s;
            " onclick="document.getElementById('input-avatar').click()">
                <i class="fa-solid fa-camera"></i>
            </button>
            <!-- Formulario oculto para subir avatar -->
            <input type="file" id="input-avatar" form="form-perfil-artista" name="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(this)">
        </div>
        
        <div class="info-basica-artista">
            <h1>{{ $artista->nombre_artistico }}</h1>
            <p id="bio-header">
                @php 
                    $bioFull = $artista->biografia ?? 'Sin biografía';
                    $posPunto = strpos($bioFull, '.');
                    $posSalto = strpos($bioFull, "\n");
                    
                    $corte = false;
                    if ($posPunto !== false && $posSalto !== false) {
                        $corte = min($posPunto, $posSalto);
                    } elseif ($posPunto !== false) {
                        $corte = $posPunto;
                    } elseif ($posSalto !== false) {
                        $corte = $posSalto;
                    }

                    if ($corte !== false) {
                         echo e(substr($bioFull, 0, $corte + 1)) . ' <span style="color: #6F00D0; font-weight: bold;">...</span>';
                    } else {
                        if (strlen($bioFull) > 100) {
                             echo e(substr($bioFull, 0, 100)) . ' <span style="color: #6F00D0; font-weight: bold;">...</span>';
                        } else {
                             echo e($bioFull);
                        }
                    }
                @endphp
            </p>
        </div>
    </div>

    <!-- Navegación Tabs -->
    <nav class="navegacion-pestanas">
        <a onclick="mostrarSeccion('albumes')" id="tab-albumes" class="pestana activa">Álbumes</a>
        <a onclick="mostrarSeccion('canciones')" id="tab-canciones" class="pestana">Canciones</a>
        <a onclick="mostrarSeccion('perfil')" id="tab-perfil" class="pestana">Perfil</a>
    </nav>

    <!-- SECCIÓN ÁLBUMES -->
    <div id="seccion-albumes" class="seccion-pestana activa">
        <!-- Barra de Acciones -->
        <section class="barra-acciones">
            <h2 class="titulo-seccion">Tus Álbumes</h2>
            <div class="grupo-botones">
                <!-- <a href="{{ route('artist.panel.album.index', $artista->artista_id) }}" class="boton-primario">Gestionar Canciones</a> -->
                <a href="{{ route('artist.panel.album.create', $artista->artista_id) }}" class="boton-primario">Subir Nuevo Álbum</a>
            </div>
        </section>

        <!-- Grid de Álbumes -->
        <section class="grid-albumes">
            @forelse ($albumes as $album)
                <article class="tarjeta-album">
                    <img src="{{ $album->portada_url ? asset($album->portada_url) : asset('multimedia/img/default-album.jpg') }}" alt="Portada {{ $album->titulo }}" class="imagen-portada">
                    
                    <div class="info-album">
                        <span class="titulo-album">{{ $album->titulo }}</span>
                        <span class="anio-album">{{ $album->fecha_lanzamiento ? \Carbon\Carbon::parse($album->fecha_lanzamiento)->year : 'N/A' }}</span>
                        
                        <div class="badge-estado">
                            @if($album->estado == 'publico')
                                <i class="fa-solid fa-earth-americas estado-publico"></i> <span class="estado-publico">Público</span>
                            @elseif($album->estado == 'privado')
                                <i class="fa-solid fa-lock estado-privado"></i> <span class="estado-privado">Privado</span>
                            @else
                                <i class="fa-regular fa-eye-slash estado-oculto"></i> <span class="estado-oculto">Oculto</span>
                            @endif
                        </div>
                    </div>

                    <div class="botones-tarjeta">
                        <a href="{{ route('artist.panel.album.edit', ['id' => $artista->artista_id, 'albumId' => $album->album_id]) }}" class="boton-tarjeta"><i class="fa-solid fa-pen"></i> Editar</a>
                        <!-- <button class="boton-tarjeta"><i class="fa-solid fa-plus"></i> Canción</button> -->
                    </div>
                </article>
            @empty
                <!-- Empty State Card -->
                <article class="tarjeta-album tarjeta-vacia">
                    <div class="icono-vacio">
                        <i class="fa-regular fa-image"></i>
                    </div>
                    <div class="texto-vacio">
                        <h3>Sube tu primer álbum</h3>
                        <p>Empieza a compartir tu música con el mundo.</p>
                    </div>
                    <a href="{{ route('artist.panel.album.create', $artista->artista_id) }}" class="boton-comenzar" style="cursor: pointer;">Comenzar</a>
                </article>
            @endforelse
            
            @if(count($albumes) > 0)
             <article class="tarjeta-album tarjeta-vacia">
                <div class="icono-vacio">
                    <i class="fa-regular fa-image"></i>
                </div>
                <div class="texto-vacio">
                    <h3>Añadir otro álbum</h3>
                    <p>Sigue ampliando tu discografía.</p>
                </div>
                <a href="{{ route('artist.panel.album.create', $artista->artista_id) }}" class="boton-comenzar" style="cursor: pointer;">Crear</a>
            </article>
            @endif
        </section>
    </div>

    <!-- SECCIÓN CANCIONES -->
    <div id="seccion-canciones" class="seccion-pestana">
        
        <!-- Barra de Filtros -->
        <section class="barra-filtros">
            <div class="buscador-canciones">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Buscar por título de canción...">
            </div>
            
            <div class="controles-filtros">
                <select class="filtro-select">
                    <option>Filtrar por Álbum</option>
                </select>
                <select class="filtro-select">
                    <option>Filtrar por Estado</option>
                </select>
                <select class="filtro-select">
                    <option>Más Recientes</option>
                </select>
            </div>
        </section>

        <section class="barra-acciones" style="margin-top: 0; margin-bottom: 20px;">
             <h2 class="titulo-seccion">Tus Pistas</h2>
        </section>

        <section class="grid-canciones">
            @forelse ($canciones as $cancion)
            <article class="tarjeta-cancion">
                <div class="icono-cancion">
                    <i class="fa-solid fa-music"></i>
                </div>
                <div class="info-cancion">
                    <span class="titulo-cancion">{{ $cancion->titulo }}</span>
                    <span class="album-cancion">{{ $cancion->nombre_album }}</span>
                    
                    <div class="meta-cancion">
                        <span><i class="fa-regular fa-clock"></i> {{ gmdate("i:s", $cancion->duracion) }}</span>
                        
                        @if($cancion->estado == 'publico')
                            <span class="badge-estado-sm badge-publico"><i class="fa-solid fa-circle" style="font-size: 6px; vertical-align: middle;"></i> Público</span>
                        @elseif($cancion->estado == 'privado')
                            <span class="badge-estado-sm badge-privado"><i class="fa-solid fa-lock" style="font-size: 8px;"></i> Privado</span>
                        @else
                            <span class="badge-estado-sm badge-oculto"><i class="fa-regular fa-eye-slash" style="font-size: 8px;"></i> Oculto</span>
                        @endif
                    </div>
                </div>
            </article>
            @empty
             <!-- Empty State Canciones -->
             <article class="tarjeta-album tarjeta-vacia" style="min-height: 200px; border-color: var(--color-morado-suave);">
                <div class="icono-vacio" style="font-size: 2rem;">
                    <i class="fa-solid fa-microphone-lines"></i>
                </div>
                <div class="texto-vacio">
                    <h3>Sube tu primera canción</h3>
                    <p>Comparte tu voz con el mundo.</p>
                </div>
                    <a href="{{ route('artist.panel.song.create.standalone', $artista->artista_id) }}" class="boton-comenzar" style="cursor: pointer;">Comenzar</a>
            </article>
            @endforelse
            
            @if(count($canciones) > 0)
             <article class="tarjeta-cancion vacia">
                <div class="icono-vacio">
                    <i class="fa-solid fa-microphone-lines"></i>
                </div>
                <div class="texto-vacio">
                    <h3>Subir nueva pista</h3>
                    <p>Añade un single o demo.</p>
                </div>
                <a href="{{ route('artist.panel.song.create.standalone', $artista->artista_id) }}" class="boton-primario">Subir</a>
            </article>
            @endif
        </section>
    </div>

    <!-- SECCIÓN PERFIL -->
    <div id="seccion-perfil" class="seccion-pestana">
        <section class="barra-acciones" style="margin-bottom: 20px;">
             <h2 class="titulo-seccion">Editar Perfil</h2>
             <button class="boton-primario" type="submit" form="form-perfil-artista">Guardar Cambios</button>
        </section>
        
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form id="form-perfil-artista" class="grid-perfil-completo" enctype="multipart/form-data" method="POST" action="{{ route('artist.panel.update', $artista->artista_id) }}">
            @csrf
            @method('PUT')
            
            <!-- Columna Izquierda: Biografía -->
            <div class="columna-bio">
                <div class="tarjeta-perfil tarjeta-bio-compacta">
                    <label class="label-bio">Biografía</label>
                    <textarea name="biografia" class="input-biografia-medium" placeholder="Escribe aquí tu trayectoria, influencias y mensaje...">{{ old('biografia', $artista->biografia) }}</textarea>
                    <p class="nota-bottom">Esta información aparecerá en tu página de artista.</p>
                </div>
            </div>

            <!-- Columna Derecha: Datos Generales (Sin Títulos) -->
            <div class="columna-datos">
                
                <div class="tarjeta-perfil">
                    <!-- Datos Identidad -->
                    <div class="grupo-input">
                        <label>Nombre Artístico</label>
                        <input type="text" name="nombre_artistico" id="input-nombre-artistico" value="{{ old('nombre_artistico', $artista->nombre_artistico) }}" class="input-linea" required>
                    </div>

                    <div class="fila-doble">
                        <div class="grupo-input">
                            <label>Nombre Real</label>
                            <input type="text" name="nombre_real" value="{{ old('nombre_real', $user->nombre_real) }}" placeholder="Tu nombre" class="input-linea">
                        </div>
                        <div class="grupo-input">
                            <label>Apellidos</label>
                            <input type="text" name="apellidos" value="{{ old('apellidos', $user->apellidos) }}" placeholder="Tus apellidos" class="input-linea">
                        </div>
                    </div>

                    <div class="separador-suave"></div>

                    <!-- Datos Cuenta -->
                    <div class="grupo-input">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-linea">
                    </div>
                    
                    <div class="grupo-input">
                        <label>Nueva Contraseña</label>
                        <div class="input-password-wrapper">
                            <input type="password" name="password" placeholder="Dejar en blanco para mantener" class="input-linea">
                            <button type="button" class="btn-toggle-pass"><i class="fa-regular fa-eye"></i></button>
                        </div>
                    </div>
                    
                    <div class="grupo-input">
                        <label>Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_confirmation" placeholder="Repite la contraseña" class="input-linea">
                    </div>

                    <div class="separador-suave"></div>

                    <!-- Datos Ubicación -->
                    <div class="fila-doble">
                        <div class="grupo-input">
                            <label>País</label>
                            <select name="pais" class="input-linea">
                                <option value="">Seleccionar...</option>
                                <option {{ (old('pais', $user->pais) == 'España') ? 'selected' : '' }}>España</option>
                                <option {{ (old('pais', $user->pais) == 'México') ? 'selected' : '' }}>México</option>
                                <option {{ (old('pais', $user->pais) == 'Colombia') ? 'selected' : '' }}>Colombia</option>
                                <option {{ (old('pais', $user->pais) == 'Argentina') ? 'selected' : '' }}>Argentina</option>
                                <option {{ (old('pais', $user->pais) == 'Estados Unidos') ? 'selected' : '' }}>Estados Unidos</option>
                            </select>
                        </div>
                        <div class="grupo-input">
                            <label>Código Postal</label>
                            <input type="text" name="codigo_postal" value="{{ old('codigo_postal', $user->codigo_postal) }}" class="input-linea">
                        </div>
                    </div>
                    <div class="grupo-input">
                        <label>Ciudad</label>
                        <input type="text" name="ciudad" value="{{ old('ciudad', $user->ciudad) }}" class="input-linea">
                    </div>
                </div>

            </div>
        </form>
    </div>

</div>

@endsection

@push('scripts')
<script>
    function mostrarSeccion(id) {
        document.querySelectorAll('.seccion-pestana').forEach(el => el.classList.remove('activa'));
        document.querySelectorAll('.pestana').forEach(el => el.classList.remove('activa'));
        document.getElementById('seccion-' + id).classList.add('activa');
        document.getElementById('tab-' + id).classList.add('activa');
    }

    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-visual').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Toggle password visibility
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtns = document.querySelectorAll('.btn-toggle-pass');
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    });

    // Upload Wizard Logic
    let currentStep = 1;
    let songCount = 0;

    function openUploadModal() {
        document.getElementById('uploadModal').style.display = 'flex';
        showStep(1);
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').style.display = 'none';
        resetForm();
    }

    function showStep(step) {
        document.querySelectorAll('.wizard-step').forEach(el => el.classList.remove('active'));
        document.getElementById('step-' + step).classList.add('active');
        currentStep = step;
        updateButtons();
    }

    function nextStep() {
        if (currentStep === 1) {
            // Validate Step 1
            const title = document.querySelector('input[name="titulo"]').value;
            if (!title) {
                alert('El título del álbum es obligatorio.');
                return;
            }
            showStep(2);
        }
    }

    function prevStep() {
        if (currentStep === 2) {
            showStep(1);
        }
    }

    function updateButtons() {
        // Logic to update Next/Prev/Submit buttons visibility/state
        const btnNext = document.getElementById('btn-next');
        const btnSubmit = document.getElementById('btn-submit');
        
        if (currentStep === 1) {
            btnNext.style.display = 'inline-block';
            btnSubmit.style.display = 'none';
        } else {
            btnNext.style.display = 'none';
            btnSubmit.style.display = 'inline-block';
            // Disable submit if no songs
            btnSubmit.disabled = songCount === 0;
            if(songCount === 0) {
                btnSubmit.style.opacity = '0.5';
                btnSubmit.style.cursor = 'not-allowed';
            } else {
                btnSubmit.style.opacity = '1';
                btnSubmit.style.cursor = 'pointer';
            }
        }
    }

    function addSongField() {
        const container = document.getElementById('songs-container');
        const index = songCount;
        
        const html = `
            <div class="song-item" id="song-${index}">
                <div class="song-header">
                    <h4>Canción #${index + 1}</h4>
                    <button type="button" class="btn-remove-song" onclick="removeSong(${index})"><i class="fa-solid fa-trash"></i></button>
                </div>
                <div class="song-body">
                    <div class="grupo-input">
                        <label>Título <span class="required">*</span></label>
                        <input type="text" name="canciones[${index}][titulo]" class="input-linea" required>
                    </div>
                    <div class="fila-doble">
                        <div class="grupo-input">
                            <label>Duración (Min)</label>
                            <input type="number" name="canciones[${index}][duracion_min]" class="input-linea" min="0" required>
                        </div>
                        <div class="grupo-input">
                            <label>Duración (Seg)</label>
                            <input type="number" name="canciones[${index}][duracion_sec]" class="input-linea" min="0" max="59" required>
                        </div>
                    </div>
                     <div class="fila-doble">
                        <div class="grupo-input">
                            <label>Estado</label>
                            <select name="canciones[${index}][estado]" class="input-linea">
                                <option value="publico">Público</option>
                                <option value="privado">Privado</option>
                                <option value="oculto">Oculto</option>
                            </select>
                        </div>
                        <div class="grupo-input">
                            <label>Créditos</label>
                            <input type="text" name="canciones[${index}][creditos]" class="input-linea" placeholder="Prod. by, feat...">
                        </div>
                    </div>
                    <div class="grupo-input">
                        <label>Letra (Opcional)</label>
                        <textarea name="canciones[${index}][letra]" class="input-linea" rows="3" placeholder="Escribe la letra aquí..."></textarea>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', html);
        songCount++;
        updateButtons();
    }

    function removeSong(index) { // Simple remove (hiding/disabling would be better for indexing, but let's just clear innerHTML for now or complex re-indexing is needed)
        // For simplicity in this iteration: Remove element and re-index? 
        // Or just hide and unset?
        // Let's just remove and warn user that this might mess up if we rely on strict index. 
        // Actually, PHP handles array keys fine. 
        // But if I delete index 0 and add, next is index 1.
        // It's safer to re-generate the list to keep indices sequential 0,1,2... or use a counter that never decrements for keys.
        // Using a unique ID for keys is better.
        
        document.getElementById('song-' + index).remove();
        songCount--;
        updateButtons();
        
        // Check if 0 songs left
        if (songCount === 0) {
             // Maybe show empty state?
        }
    }
    
    // Initial song field
    // addSongField(); 

    function resetForm() {
        document.getElementById('form-upload-album').reset();
        document.getElementById('songs-container').innerHTML = '';
        songCount = 0;
        showStep(1);
    }

</script>
@endpush

<!-- MODAL UPLOAD -->
<div id="uploadModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <button type="button" class="btn-close-modal" onclick="closeUploadModal()"><i class="fa-solid fa-xmark"></i></button>
        
        <form id="form-upload-album" action="{{ route('artist.panel.album.store', $artista->artista_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- STEP 1: Album Info -->
            <div id="step-1" class="wizard-step active">
                <h2 class="modal-title">Nuevo Álbum</h2>
                <div class="grupo-input">
                    <label>Título del Álbum <span class="required">*</span></label>
                    <input type="text" name="titulo" class="input-linea" placeholder="Ej. Vida Cotidiana" required>
                </div>
                <div class="grupo-input">
                    <label>Portada</label>
                    <input type="file" name="portada" class="input-linea" accept="image/*">
                </div>
                <div class="fila-doble">
                    <div class="grupo-input">
                        <label>Fecha Lanzamiento</label>
                        <input type="date" name="fecha_lanzamiento" class="input-linea" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="grupo-input">
                        <label>Privacidad</label>
                        <select name="estado" class="input-linea">
                            <option value="publico">Público</option>
                            <option value="privado">Privado</option>
                            <option value="oculto">Oculto</option>
                        </select>
                    </div>
                </div>
                <div class="grupo-input">
                    <label>Contexto / Descripción</label>
                    <textarea name="contexto" class="input-linea" rows="3"></textarea>
                </div>
            </div>

            <!-- STEP 2: Songs -->
            <div id="step-2" class="wizard-step">
                <div class="header-songs">
                    <h2 class="modal-title">Añadir Canciones</h2>
                    <button type="button" class="btn-add-song" onclick="addSongField()"><i class="fa-solid fa-plus"></i> Añadir Canción</button>
                </div>
                
                <div id="songs-container" class="songs-scroll-container">
                    <!-- Dynamic Songs Here -->
                    <div class="empty-songs-msg text-center" style="padding: 20px; color: #888;">
                        <p>No hay canciones añadidas. Pulsa "Añadir Canción" para empezar.</p>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="modal-footer">
                <button type="button" class="btn-prev" onclick="prevStep()">Atrás</button>
                <div class="spacer"></div>
                <button type="button" class="btn-next" id="btn-next" onclick="nextStep()">Siguiente</button>
                <button type="submit" class="btn-submit" id="btn-submit" style="display: none;">Publicar Álbum</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.modal-content {
    background: #1a1a1a;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    position: relative;
    max-height: 90vh;
    overflow-y: auto;
    color: white;
}
.btn-close-modal {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
}
.wizard-step {
    display: none;
}
.wizard-step.active {
    display: block;
}
.modal-title {
    margin-bottom: 20px;
    color: #6F00D0;
}
.modal-footer {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
}
.btn-next, .btn-submit {
    background: #6F00D0;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
.btn-prev {
    background: transparent;
    color: #888;
    border: 1px solid #888;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
}
.btn-add-song {
    background: #28a745;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
}
.header-songs {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}
.songs-scroll-container {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #333;
    padding: 10px;
    border-radius: 6px;
    background: #222;
}
.song-item {
    background: #2a2a2a;
    border: 1px solid #444;
    border-radius: 6px;
    margin-bottom: 10px;
    padding: 10px;
}
.song-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    border-bottom: 1px solid #444;
    padding-bottom: 5px;
}
.btn-remove-song {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
}
.required {
    color: #dc3545;
}
</style>


