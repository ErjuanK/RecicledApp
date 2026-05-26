@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <form action="{{ route('artist.panel.song.update', ['id' => $artista->artista_id, 'albumId' => $album->album_id, 'cancionId' => $cancion->cancion_id]) }}" method="POST" class="bg-white rounded-lg shadow-sm p-6 max-w-[1000px] mx-auto">
        @csrf
        @method('PUT')
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-200">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Editar Canción: {{ $cancion->titulo }}</h1>
                <p class="text-gray-500 mt-1">Álbum: {{ $album->titulo }}</p>
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

        <div class="grid grid-cols-1 gap-6">
            <div class="flex flex-col gap-2">
                <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Título de la Canción <span class="text-red-500">*</span></label>
                <input name="titulo" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-gray-400" value="{{ old('titulo', $cancion->titulo) }}" type="text" required/>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Duración <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-2">
                        <input name="duracion_min" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-center placeholder:text-gray-400" placeholder="Min" type="number" min="0" value="{{ old('duracion_min', floor($cancion->duracion / 60)) }}" required/>
                        <span class="font-bold text-gray-400">:</span>
                        <input name="duracion_sec" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-center placeholder:text-gray-400" placeholder="Seg" type="number" min="0" max="59" value="{{ old('duracion_sec', $cancion->duracion % 60) }}" required/>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Visibilidad</label>
                    <div class="flex items-center gap-4 h-[50px]">
                        <input type="hidden" name="estado" id="input-estado" value="{{ old('estado', $cancion->estado) }}">
                        <div class="flex p-1 bg-gray-100 rounded-lg border border-gray-200 inline-flex w-full h-full">
                            <button type="button" onclick="setPrivacy('publico')" id="btn-publico" class="flex-1 rounded-md text-sm font-medium {{ old('estado', $cancion->estado) == 'publico' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }} transition-all">Público</button>
                            <button type="button" onclick="setPrivacy('privado')" id="btn-privado" class="flex-1 rounded-md text-sm font-medium {{ old('estado', $cancion->estado) == 'privado' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }} transition-all">Privado</button>
                            <button type="button" onclick="setPrivacy('oculto')" id="btn-oculto" class="flex-1 rounded-md text-sm font-medium {{ old('estado', $cancion->estado) == 'oculto' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }} transition-all">Oculto</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Créditos (Opcional)</label>
                <textarea name="creditos" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all resize-none placeholder:text-gray-400" rows="2">{{ old('creditos', $cancion->creditos) }}</textarea>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Contexto / Historia (Opcional)</label>
                <textarea name="contexto" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all resize-none placeholder:text-gray-400" rows="3">{{ old('contexto', $cancion->contexto) }}</textarea>
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
