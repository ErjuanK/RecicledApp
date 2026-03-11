@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Editar Canción: {{ $cancion->titulo }}</h4>
                    <small class="text-muted">Álbum: {{ $album->titulo }}</small>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('artist.song.update', [$album->album_id, $cancion->cancion_id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título de la Canción <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="titulo" name="titulo" value="{{ old('titulo', $cancion->titulo) }}" required>
                        </div>

                        <div class="row g-3 mb-3">
                             <div class="col-md-6">
                                <label class="form-label">Duración <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="duracion_min" placeholder="Min" min="0" value="{{ old('duracion_min', floor($cancion->duracion / 60)) }}" required>
                                    <span class="input-group-text">:</span>
                                    <input type="number" class="form-control" name="duracion_sec" placeholder="Seg" min="0" max="59" value="{{ old('duracion_sec', $cancion->duracion % 60) }}" required>
                                </div>
                             </div>
                             <div class="col-md-6">
                                <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="publico" {{ old('estado', $cancion->estado) == 'publico' ? 'selected' : '' }}>Público</option>
                                    <option value="privado" {{ old('estado', $cancion->estado) == 'privado' ? 'selected' : '' }}>Privado</option>
                                    <option value="oculto" {{ old('estado', $cancion->estado) == 'oculto' ? 'selected' : '' }}>Oculto</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="creditos" class="form-label">Créditos (Opcional)</label>
                            <textarea class="form-control" id="creditos" name="creditos" rows="2">{{ old('creditos', $cancion->creditos) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="contexto" class="form-label">Contexto / Historia (Opcional)</label>
                            <textarea class="form-control" id="contexto" name="contexto" rows="3">{{ old('contexto', $cancion->contexto) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('artist.album.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
