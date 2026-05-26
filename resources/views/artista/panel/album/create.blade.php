@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <form action="{{ route('artist.panel.album.store', $artista->artista_id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm p-6">
        @csrf
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-200">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Crear Nuevo Álbum</h1>
                <p class="text-gray-500 mt-1">Completa los detalles de tu nuevo lanzamiento</p>
            </div>
            <!-- Removed X button as requested -->
        </div>

        <!-- Content Area -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- Left: Art Upload -->
            <div class="lg:col-span-4 flex flex-col gap-4">
                <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Portada del Álbum</label>
                <div class="aspect-square w-full rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 flex flex-col items-center justify-center gap-4 hover:border-primary transition-colors group cursor-pointer relative overflow-hidden">
                    <!-- Preview Image -->
                    <img id="preview-image" src="#" alt="Preview" class="absolute inset-0 w-full h-full object-cover hidden">
                    
                    <div id="upload-placeholder" class="relative z-10 flex flex-col items-center p-4 text-center">
                        <span class="material-symbols-outlined text-4xl text-gray-400 group-hover:text-primary mb-2">cloud_upload</span>
                        <span class="text-gray-900 font-medium">Subir Portada</span>
                        <span class="text-xs text-gray-500 mt-1">Min 3000 x 3000px (JPG/PNG)</span>
                    </div>
                    <input type="file" name="portada" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" accept="image/*" onchange="previewFile(this)">
                </div>
            </div>
            
            <!-- Right: Metadata Input -->
            <div class="lg:col-span-8 flex flex-col gap-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Título del Álbum <span class="text-red-500">*</span></label>
                        <input name="titulo" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-gray-400" placeholder="Ej. Vida Cotidiana" type="text" required/>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Fecha Lanzamiento</label>
                        <input name="fecha_lanzamiento" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" type="date" value="{{ date('Y-m-d') }}"/>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Contexto & Descripción</label>
                    <textarea name="contexto" class="w-full h-32 bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all resize-none placeholder:text-gray-400" placeholder="Añade una descripción, contexto o historia detrás del álbum..."></textarea>
                </div>

                <!-- Visibility Toggle integrated here for better flow -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Visibilidad</label>
                    <div class="flex items-center gap-4">
                        <input type="hidden" name="estado" id="input-estado" value="publico">
                        <div class="flex p-1 bg-gray-100 rounded-lg border border-gray-200 inline-flex">
                            <button type="button" onclick="setPrivacy('publico')" id="btn-publico" class="px-4 py-2 rounded-md text-sm font-medium bg-white text-gray-900 shadow-sm transition-all">Público</button>
                            <button type="button" onclick="setPrivacy('privado')" id="btn-privado" class="px-4 py-2 rounded-md text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Privado</button>
                            <button type="button" onclick="setPrivacy('oculto')" id="btn-oculto" class="px-4 py-2 rounded-md text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Oculto</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-10 border-gray-200">

        <!-- Tracks Section -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-bold text-gray-900">Canciones del Álbum</h2>
                    <span class="bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full" id="track-count-badge">0 Canciones</span>
                </div>
                <button type="button" onclick="addTrackRow()" class="flex items-center gap-2 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-800 px-4 py-2 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">add</span>
                    Añadir Canción
                </button>
            </div>
            
            <!-- Track List Container -->
            <div id="tracks-container" class="space-y-3">
                <!-- Dynamic Tracks will be added here -->
                
                <!-- Empty State -->
                <div id="empty-state" class="border-2 border-dashed border-gray-300 rounded-xl p-10 flex flex-col items-center justify-center text-gray-400 bg-gray-50">
                    <span class="material-symbols-outlined mb-2 text-4xl text-gray-300">queue_music</span>
                    <p class="text-base font-medium text-gray-500">Aún no hay canciones</p>
                    <p class="text-sm">Añade pistas para completar tu álbum</p>
                </div>
            </div>
        </div>

        <hr class="my-10 border-gray-200">

        <!-- Footer Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('artist.panel.dashboard', $artista->artista_id) }}" class="px-6 py-3 rounded-lg text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">Cancelar</a>
            <button type="submit" class="px-8 py-3 rounded-lg bg-primary text-white text-sm font-bold shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:translate-y-[-1px] active:translate-y-[0px] transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span>
                Guardar Álbum
            </button>
        </div>

    </form>
</div>

@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script>
    tailwind.config = {
        corePlugins: {
            preflight: false,
        },
        theme: {
            extend: {
                colors: {
                    "primary": "#7f13ec",
                }
            }
        }
    }
</script>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>
@endpush

@push('scripts')
<script>
    let trackIndex = 0;

    function previewFile(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('preview-image');
                const placeholder = document.getElementById('upload-placeholder');
                img.src = e.target.result;
                img.classList.remove('hidden');
                placeholder.classList.add('opacity-0'); 
            }
            reader.readAsDataURL(file);
        }
    }

    function setPrivacy(status) {
        document.getElementById('input-estado').value = status;
        const buttons = ['publico', 'privado', 'oculto'];
        buttons.forEach(btn => {
            const el = document.getElementById('btn-' + btn);
            if (btn === status) {
                el.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                el.classList.remove('text-gray-500', 'hover:text-gray-900');
            } else {
                el.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                el.classList.add('text-gray-500', 'hover:text-gray-900');
            }
        });
    }

    function addTrackRow() {
        const container = document.getElementById('tracks-container');
        const emptyState = document.getElementById('empty-state');
        if(emptyState) emptyState.style.display = 'none';

        const rowId = `track-${trackIndex}`;
        const html = `
            <div id="${rowId}" class="flex flex-col md:flex-row items-start gap-4 p-4 bg-white rounded-lg border border-gray-200 hover:border-primary/50 transition-all shadow-sm group">
                <div class="mt-2 text-gray-400 cursor-move">
                    <span class="material-symbols-outlined">drag_indicator</span>
                </div>
                
                <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-4 w-full">
                    <!-- Title -->
                    <div class="md:col-span-4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Título</label>
                        <input type="text" name="canciones[${trackIndex}][titulo]" placeholder="Nombre de la canción" class="w-full bg-gray-50 border border-gray-200 rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none transition-colors" required>
                    </div>
                    
                    <!-- Duration -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Duración</label>
                        <div class="flex gap-2">
                            <input type="number" name="canciones[${trackIndex}][duracion_min]" placeholder="Min" class="w-full bg-gray-50 border border-gray-200 rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none" min="0" required>
                            <input type="number" name="canciones[${trackIndex}][duracion_sec]" placeholder="Seg" class="w-full bg-gray-50 border border-gray-200 rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none" min="0" max="59" required>
                        </div>
                    </div>

                    <!-- Privacy -->
                    <div class="md:col-span-2">
                         <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
                         <select name="canciones[${trackIndex}][estado]" class="w-full bg-gray-50 border border-gray-200 rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none text-gray-700">
                            <option value="publico">Público</option>
                            <option value="privado">Privado</option>
                            <option value="oculto">Oculto</option>
                        </select>
                    </div>

                    <!-- Credits -->
                    <div class="md:col-span-4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Créditos</label>
                        <input type="text" name="canciones[${trackIndex}][creditos]" placeholder="Prod., Feat., etc." class="w-full bg-gray-50 border border-gray-200 rounded-md px-3 py-2 text-sm focus:ring-1 focus:ring-primary outline-none">
                    </div>
                </div>

                <div class="mt-1">
                    <button type="button" onclick="removeTrack('${rowId}')" class="p-2 bg-red-50 text-red-500 rounded-md hover:bg-red-100 transition-colors" title="Eliminar canción">
                        <span class="material-symbols-outlined text-xl">delete</span>
                    </button>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);
        trackIndex++;
        updateTrackCount();
    }

    function removeTrack(id) {
        document.getElementById(id).remove();
        updateTrackCount();
        
        const container = document.getElementById('tracks-container');
        // Check if only empty-state div remains (it might be hidden but present)
        // Or if strictly counting children elements
        const rows = container.querySelectorAll('[id^="track-"]');
        if (rows.length === 0) {
             document.getElementById('empty-state').style.display = 'flex';
        }
    }

    function updateTrackCount() {
        const container = document.getElementById('tracks-container');
        const rows = container.querySelectorAll('[id^="track-"]');
        document.getElementById('track-count-badge').innerText = `${rows.length} Canciones`;
    }
</script>
@endpush
