@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <form action="{{ route('artist.panel.album.update', ['id' => $artista->artista_id, 'albumId' => $album->album_id]) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm p-6 max-w-[1000px] mx-auto">
        @csrf
        @method('PUT')
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-200">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Editar Álbum: {{ $album->titulo }}</h1>
                <p class="text-gray-500 mt-1">Actualiza los detalles de tu lanzamiento</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left: Art Upload -->
            <div class="lg:col-span-4 flex flex-col gap-4">
                <label class="block text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Portada del Álbum</label>
                <div class="aspect-square w-full rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 flex flex-col items-center justify-center gap-4 hover:border-primary transition-colors group cursor-pointer relative overflow-hidden">
                    <img id="preview-image" src="{{ asset($album->portada_url ?? 'multimedia/img/default-album.jpg') }}" alt="Preview" class="absolute inset-0 w-full h-full object-cover">
                    
                    <div id="upload-placeholder" class="relative z-10 flex flex-col items-center p-4 text-center opacity-0 hover:opacity-100 transition-opacity bg-black/40 w-full h-full justify-center">
                        <span class="material-symbols-outlined text-4xl text-white mb-2">cloud_upload</span>
                        <span class="text-white font-medium">Actualizar Portada</span>
                        <span class="text-xs text-gray-200 mt-1">Min 3000 x 3000px</span>
                    </div>
                    <input type="file" name="portada" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-20" accept="image/*" onchange="previewFile(this)">
                </div>
            </div>
            
            <!-- Right: Metadata Input -->
            <div class="lg:col-span-8 flex flex-col gap-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Título del Álbum <span class="text-red-500">*</span></label>
                        <input name="titulo" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-gray-400" value="{{ old('titulo', $album->titulo) }}" type="text" required/>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Fecha Lanzamiento</label>
                        <input name="fecha_lanzamiento" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" type="date" value="{{ old('fecha_lanzamiento', $album->fecha_lanzamiento) }}"/>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Contexto & Descripción</label>
                    <textarea name="contexto" class="w-full h-32 bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all resize-none placeholder:text-gray-400">{{ old('contexto', $album->contexto) }}</textarea>
                </div>

                <!-- Visibility -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Visibilidad</label>
                    <div class="flex items-center gap-4">
                        <input type="hidden" name="estado" id="input-estado" value="{{ old('estado', $album->estado) }}">
                        <div class="flex p-1 bg-gray-100 rounded-lg border border-gray-200 inline-flex">
                            <button type="button" onclick="setPrivacy('publico')" id="btn-publico" class="px-4 py-2 rounded-md text-sm font-medium {{ old('estado', $album->estado) == 'publico' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }} transition-all">Público</button>
                            <button type="button" onclick="setPrivacy('privado')" id="btn-privado" class="px-4 py-2 rounded-md text-sm font-medium {{ old('estado', $album->estado) == 'privado' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }} transition-all">Privado</button>
                            <button type="button" onclick="setPrivacy('oculto')" id="btn-oculto" class="px-4 py-2 rounded-md text-sm font-medium {{ old('estado', $album->estado) == 'oculto' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }} transition-all">Oculto</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-8 border-gray-200">

        <!-- Footer Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('artist.panel.album.index', $artista->artista_id) }}" class="px-6 py-3 rounded-lg text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">Cancelar</a>
            <button type="submit" class="px-8 py-3 rounded-lg bg-primary text-white text-sm font-bold shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:translate-y-[-1px] active:translate-y-[0px] transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span>
                Guardar Cambios
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
    function previewFile(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('preview-image');
                img.src = e.target.result;
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
