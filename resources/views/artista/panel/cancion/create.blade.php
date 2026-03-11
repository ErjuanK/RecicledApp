@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <form action="{{ route('artist.panel.song.store.standalone', $artista->artista_id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm p-6 max-w-[1000px] mx-auto">
        @csrf
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-200">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Añadir Nueva Canción</h1>
                <p class="text-gray-500 mt-1">Sube un sencillo o una canción suelta</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
             <!-- Left: Metadata Input (User requested image on Right, but standardized layouts usually put image left. 
             Wait, user said: "haz un contenedor parecido al que has creado anteriormente para el crear album hacia la derecha"
             This implies moving the image container to the RIGHT.
             Let's try that layout: Inputs Left, Image Right.) -->
            
            <div class="lg:col-span-8 flex flex-col gap-6 order-2 lg:order-1">
                 <!-- Album Selection (Optional) -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Seleccionar Álbum (Opcional)</label>
                    <select name="album_id" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        <option value="">-- Sin Álbum (Single) --</option>
                        @foreach($albumes as $album)
                            <option value="{{ $album->album_id }}">{{ $album->titulo }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500">Si no seleccionas un álbum, se guardará como Single.</p>
                </div>

                <!-- Song Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Título de la Canción <span class="text-red-500">*</span></label>
                        <input name="titulo" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-gray-400" placeholder="Ej. Midnight City" type="text" required/>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Duración <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <input type="number" name="duracion_min" placeholder="Min" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" min="0" required>
                            <input type="number" name="duracion_sec" placeholder="Seg" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" min="0" max="59" required>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Visibility -->
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Visibilidad</label>
                        <div class="flex items-center gap-4">
                            <input type="hidden" name="estado" id="input-estado" value="publico">
                            <div class="flex p-1 bg-gray-100 rounded-lg border border-gray-200 inline-flex w-full">
                                <button type="button" onclick="setPrivacy('publico')" id="btn-publico" class="flex-1 px-4 py-2 rounded-md text-sm font-medium bg-white text-gray-900 shadow-sm transition-all">Público</button>
                                <button type="button" onclick="setPrivacy('privado')" id="btn-privado" class="flex-1 px-4 py-2 rounded-md text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Privado</button>
                                <button type="button" onclick="setPrivacy('oculto')" id="btn-oculto" class="flex-1 px-4 py-2 rounded-md text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Oculto</button>
                            </div>
                        </div>
                    </div>

                    <!-- Credits -->
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Créditos</label>
                        <input name="creditos" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-gray-400" placeholder="Prod. by, Feat..." type="text"/>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Contexto</label>
                    <textarea name="contexto" class="w-full h-24 bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all resize-none placeholder:text-gray-400" placeholder="Breve descripción o contexto..."></textarea>
                </div>

                 <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Letra (Opcional)</label>
                    <textarea name="letra" class="w-full h-40 bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all resize-none placeholder:text-gray-400" placeholder="Escribe la letra de la canción aquí..."></textarea>
                </div>
            </div>

            <!-- Right: Art Upload -->
            <div class="lg:col-span-4 flex flex-col gap-4 order-1 lg:order-2">
                <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Portada del Sencillo</label>
                <div class="aspect-square w-full rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 flex flex-col items-center justify-center gap-4 hover:border-primary transition-colors group cursor-pointer relative overflow-hidden sticky top-6">
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

        </div>

        <hr class="my-8 border-gray-200">

        <!-- Footer Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('artist.panel.dashboard', $artista->artista_id) }}" class="px-6 py-3 rounded-lg text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">Cancelar</a>
            <button type="submit" class="px-8 py-3 rounded-lg bg-primary text-white text-sm font-bold shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:translate-y-[-1px] active:translate-y-[0px] transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span>
                Guardar Canción
            </button>
        </div>

    </form>
</div>

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
</script>
@endpush
