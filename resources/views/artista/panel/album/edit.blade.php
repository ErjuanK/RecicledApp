@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Editar Álbum: {{ $album->titulo }}</h4>
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

                    <form action="{{ route('artist.album.update', $album->album_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título del Álbum <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="titulo" name="titulo" value="{{ old('titulo', $album->titulo) }}" required>
                        </div>

                        <div class="mb-3">
                             <label for="portada" class="form-label">Actualizar Portada</label>
                             <div class="d-flex align-items-center gap-3">
                                <img src="{{ asset($album->portada_url ?? 'multimedia/img/default-album.jpg') }}" alt="Portada Actual" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                <input type="file" class="form-control" id="portada" name="portada" accept="image/*">
                             </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="fecha_lanzamiento" class="form-label">Fecha de Lanzamiento</label>
                                <input type="date" class="form-control" id="fecha_lanzamiento" name="fecha_lanzamiento" value="{{ old('fecha_lanzamiento', $album->fecha_lanzamiento) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="publico" {{ old('estado', $album->estado) == 'publico' ? 'selected' : '' }}>Público</option>
                                    <option value="privado" {{ old('estado', $album->estado) == 'privado' ? 'selected' : '' }}>Privado</option>
                                    <option value="oculto" {{ old('estado', $album->estado) == 'oculto' ? 'selected' : '' }}>Oculto</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contexto" class="form-label">Contexto / Descripción</label>
                            <textarea class="form-control" id="contexto" name="contexto" rows="4">{{ old('contexto', $album->contexto) }}</textarea>
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
