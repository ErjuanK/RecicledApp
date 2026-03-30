

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/estilo-cancion.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<main class="contenedor-cancion">
    
    <div class="diseño-desglose">
        <aside class="panel-informacion">
            <div class="portada-cancion-grande">
                <img src="<?php echo e($cancion->portada_url); ?>" alt="<?php echo e($cancion->titulo); ?>">
            </div>
            
            <div class="info-metadatos">
                <h1 class="titulo-metadatos"><?php echo e($cancion->titulo); ?></h1>
                <h2 class="artista-metadatos">
                    <a href="<?php echo e(route('artista.show', $cancion->artista_id)); ?>" style="text-decoration: none; color: inherit;">
                        <?php echo e($cancion->artista); ?>

                    </a>
                </h2>
                <div class="detalles-metadatos">
                    <p>
                        <i class="fa-solid fa-record-vinyl"></i> 
                        <a href="<?php echo e(route('album.show', $cancion->album_id)); ?>" style="text-decoration: none; color: inherit;">
                            <?php echo e($cancion->album); ?>

                        </a>
                    </p>
                    <p><i class="fa-regular fa-clock"></i> <?php echo e($cancion->duracion); ?></p>
                </div>
            </div>

            <div class="creditos-metadatos">
                <h3>Créditos</h3>
                <ul class="lista-creditos-simple">
                    <?php $__currentLoopData = $cancion->creditos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $credito): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <span class="rol"><?php echo e($credito['rol']); ?></span>
                        <span class="nombre"><?php echo e($credito['nombres']); ?></span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </aside>

        <!-- Panel Derecho: Letra Scrolleable -->
        <section class="panel-letra" id="lyrics-panel">
            <h3 class="encabezado-letra-minimal">Letra</h3>
            
            <div class="contenido-letra-limpio position-relative" id="lyrics-content">
                <?php if(!empty($cancion->letra_html)): ?>
                    <div class="letra-real">
                        <?php echo $cancion->letra_html; ?>

                    </div>
                <?php else: ?>
                    <?php $__currentLoopData = $cancion->letra_simulada; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parrafo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p><?php echo e($parrafo); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
            
            <!-- Annotation Tooltip Button -->
            <button id="annotate-btn" class="btn btn-dark btn-sm position-absolute" style="display: none; z-index: 1000; transform: translateX(-50%);">
                <i class="fa-solid fa-pen"></i> Anotar
            </button>
        </section>
        
        <!-- Annotation Sidebar / Detail Panel -->
        <aside class="panel-anotacion" id="annotation-sidebar" style="display:none; width: 300px; padding: 20px; background: #fff; border-left: 1px solid #eee;">
             <h4 class="mb-3">Anotación</h4>
             <div id="annotation-display">
                 <blockquote class="blockquote fs-6" id="annotation-text"></blockquote>
                 <p class="text-muted small">Por <span id="annotation-author" class="fw-bold"></span></p>
             </div>
             <button class="btn btn-sm btn-outline-secondary mt-3" onclick="closeAnnotationSidebar()">Cerrar</button>
        </aside>

    </div>

    <!-- Modal for Creating Annotation -->
    <div class="modal fade" id="createAnnotationModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Añadir Anotación / Conocimiento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted small mb-2">Seleccionado:</p>
            <blockquote class="blockquote fs-6 border-start border-primary ps-3" id="modal-selected-text"></blockquote>
            
            <form id="annotation-form">
                <div class="mb-3">
                    <label for="explicacion" class="form-label">Tu explicación o dato curioso:</label>
                    <textarea class="form-control" id="explicacion" rows="4" required placeholder="Escribe algo interesante sobre esta línea..."></textarea>
                </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="save-annotation-btn">Guardar Anotación</button>
          </div>
        </div>
      </div>
    </div>

</main>

<?php if(isset($cancion->letra_id)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lyricsContent = document.getElementById('lyrics-content');
        const annotateBtn = document.getElementById('annotate-btn');
        const modal = new bootstrap.Modal(document.getElementById('createAnnotationModal'));
        const modalSelectedText = document.getElementById('modal-selected-text');
        const saveBtn = document.getElementById('save-annotation-btn');
        let currentSelection = null;
        const loggedIn = <?php echo e(Auth::check() ? 'true' : 'false'); ?>;
        const letraId = <?php echo e($cancion->letra_id); ?>;
        
        // 1. Highlight Existing Annotations
        const existingAnnotations = <?php echo json_encode($cancion->anotaciones ?? [], 15, 512) ?>;
        
        // Very basic text replacement implementation (Simple approach for MVP)
        // Ideally we use offsets, but Genius HTML structure is complex. 
        // We will try to replace text if it matches exactly unique string.
        if(existingAnnotations.length > 0) {
             let html = lyricsContent.innerHTML;
             existingAnnotations.forEach(anotacion => {
                 // Escape regex special chars
                 const safeText = anotacion.texto_seleccionado.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                 const regex = new RegExp(`(${safeText})`, 'i'); 
                 // Replace only first occurrence per annotation ID logic or smarter way?
                 // For MVP, replace text with span
                 html = html.replace(regex, `<span class="annotated-text text-primary fw-bold" style="cursor:pointer; background-color: rgba(255, 235, 59, 0.3);" data-id="${anotacion.id}" data-text="${anotacion.texto_seleccionado}" data-expl="${anotacion.explicacion}" data-author="${anotacion.usuario ? anotacion.usuario.nombre_usuario : 'Usuario'}">$1</span>`);
             });
             lyricsContent.innerHTML = html;
        }

        // 2. Handle Text Selection
        lyricsContent.addEventListener('mouseup', function(e) {
            if(!loggedIn) return;

            const selection = window.getSelection();
            const text = selection.toString().trim();
            
            if (text.length > 0 && lyricsContent.contains(selection.anchorNode)) {
                // Show Button
                const range = selection.getRangeAt(0);
                const rect = range.getBoundingClientRect();
                
                // Position button above selection
                annotateBtn.style.top = (rect.top + window.scrollY - 40) + 'px';
                annotateBtn.style.left = (rect.left + (rect.width / 2)) + 'px';
                annotateBtn.style.display = 'block';
                
                currentSelection = text;
            } else {
                annotateBtn.style.display = 'none';
            }
        });

        // 3. Open Modal
        annotateBtn.addEventListener('click', function() {
            modalSelectedText.textContent = currentSelection;
            modal.show();
            annotateBtn.style.display = 'none'; // Hide button
        });

        // 4. Save Annotation
        saveBtn.addEventListener('click', function() {
            const explicacion = document.getElementById('explicacion').value;
            if(!explicacion) return alert('Escribe una explicación');

            fetch("<?php echo e(route('api.anotacion.store')); ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({
                    letra_id: letraId,
                    texto_seleccionado: currentSelection,
                    explicacion: explicacion
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    modal.hide();
                    location.reload(); // Reload to show new annotation
                } else {
                    alert('Error al guardar');
                }
            })
            .catch(err => console.error(err));
        });

        // 5. Click on Annotation
        lyricsContent.addEventListener('click', function(e) {
            if(e.target.classList.contains('annotated-text')) {
                const expl = e.target.getAttribute('data-expl');
                const author = e.target.getAttribute('data-author');
                
                document.getElementById('annotation-text').textContent = expl;
                document.getElementById('annotation-author').textContent = author;
                document.getElementById('annotation-sidebar').style.display = 'block';
            }
        });
    });
    
    function closeAnnotationSidebar() {
        document.getElementById('annotation-sidebar').style.display = 'none';
    }
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\RecicledApp\resources\views/cancion.blade.php ENDPATH**/ ?>