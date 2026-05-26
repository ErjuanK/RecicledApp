@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        <ul class="list-disc pl-5 text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center space-x-4">
            <div class="h-12 w-12 flex-shrink-0">
                <img class="h-12 w-12 rounded-lg object-cover border border-gray-200 shadow-sm"
                     src="{{ $cancion->portada ?? ($cancion->album->portada_url ?? null) ? asset($cancion->portada ?? $cancion->album->portada_url) : 'https://ui-avatars.com/api/?name='.urlencode($cancion->titulo).'&background=6F00D0&color=fff' }}"
                     alt="">
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">Editar Canción</h3>
                <p class="text-sm text-gray-500">
                    {{ $cancion->artista->nombre_artistico ?? 'Sin artista' }}
                    @if($cancion->album) · {{ $cancion->album->titulo }} @endif
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.canciones.update', $cancion->cancion_id) }}" class="px-6 py-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Título <span class="text-red-500">*</span></label>
                <input type="text" name="titulo"
                       value="{{ old('titulo', $cancion->titulo) }}"
                       required
                       class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Artista</label>
                    <input type="text"
                           value="{{ $cancion->artista->nombre_artistico ?? 'Sin artista' }}"
                           disabled
                           class="w-full border border-gray-100 bg-gray-50 rounded-lg px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Duración</label>
                    <input type="text"
                           value="{{ $cancion->duracion ? floor($cancion->duracion/60).':'.str_pad($cancion->duracion%60, 2, '0', STR_PAD_LEFT) : '-' }}"
                           disabled
                           class="w-full border border-gray-100 bg-gray-50 rounded-lg px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Visibilidad <span class="text-red-500">*</span></label>
                <div class="flex p-1 bg-gray-100 rounded-lg border border-gray-200 w-full">
                    @foreach(['publico' => 'Público', 'privado' => 'Privado', 'oculto' => 'Oculto'] as $val => $label)
                    <label class="flex-1 text-center cursor-pointer">
                        <input type="radio" name="estado" value="{{ $val }}" class="sr-only peer"
                               {{ old('estado', $cancion->estado) === $val ? 'checked' : '' }}>
                        <span class="block w-full py-1.5 text-sm font-medium rounded-md transition-all
                                     peer-checked:bg-white peer-checked:text-gray-900 peer-checked:shadow-sm
                                     text-gray-500 hover:text-gray-900">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            @if($cancion->creditos)
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Créditos</label>
                <textarea name="creditos" rows="2"
                          class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary resize-none transition-all">{{ old('creditos', $cancion->creditos) }}</textarea>
            </div>
            @endif

            <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('admin.canciones') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                    ← Volver
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-primary-dark transition-colors">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
