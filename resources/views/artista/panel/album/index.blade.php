@extends('layouts.app')

@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('artist.panel') }}">
                            <span data-feather="home"></span>
                            Perfil Artista
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('artist.album.index') }}">
                            <span data-feather="file"></span>
                            Mis Álbumes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="#">
                            <span data-feather="music"></span>
                            Mis Canciones (Ver en Álbumes)
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestionar Álbumes</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('artist.album.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-plus"></i> Nuevo Álbum
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row row-cols-1 row-cols-md-3 g-4">
                @forelse($albumes as $album)
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <img src="{{ asset($album->portada_url ?? 'multimedia/img/default-album.jpg') }}" class="card-img-top" alt="{{ $album->titulo }}" style="height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">{{ $album->titulo }}</h5>
                                <p class="card-text text-muted small">
                                    Lanzamiento: {{ $album->fecha_lanzamiento }}
                                </p>
                                <span class="badge bg-{{ $album->estado == 'publico' ? 'success' : ($album->estado == 'privado' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($album->estado) }}
                                </span>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                <a href="{{ route('artist.album.edit', $album->album_id) }}" class="btn btn-sm btn-outline-secondary">Editar Álbum</a>
                                
                                <form action="{{ route('artist.album.destroy', $album->album_id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este álbum y todas sus canciones?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </div>
                            
                            <!-- Songs List inside Card -->
                            <div class="card-footer bg-light">
                                <small class="fw-bold mb-2 d-block">Canciones ({{ $album->canciones->count() }})</small>
                                <ul class="list-group list-group-flush small mb-2">
                                    @foreach($album->canciones as $cancion)
                                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0 py-1">
                                            <span>{{ $cancion->titulo }}</span>
                                            <div>
                                                 <a href="{{ route('artist.song.edit', [$album->album_id, $cancion->cancion_id]) }}" class="text-primary me-2"><i class="fa-solid fa-pen-to-square"></i></a>
                                                 
                                                 <form action="{{ route('artist.song.destroy', [$album->album_id, $cancion->cancion_id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0 border-0" onclick="return confirm('¿Eliminar canción?')"><i class="fa-solid fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <a href="{{ route('artist.song.create', $album->album_id) }}" class="btn btn-sm btn-primary w-100">+ Añadir Canción</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fa-solid fa-compact-disc fa-4x text-muted mb-3"></i>
                            <h3>No tienes álbumes aún</h3>
                            <p class="text-muted">¡Empieza creando tu primer álbum para subir música!</p>
                            <a href="{{ route('artist.album.create') }}" class="btn btn-primary">Crear Álbum</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
