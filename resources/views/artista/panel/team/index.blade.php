@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('artist.panel.dashboard', $artista->artista_id) }}">
                            <span data-feather="home"></span>
                            Perfil Artista
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('artist.panel.album.index', $artista->artista_id) }}">
                            <span data-feather="file"></span>
                            Mis Álbumes
                        </a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link active" href="{{ route('artist.panel.team.index', $artista->artista_id) }}">
                            <span data-feather="users"></span>
                            Equipo (Editores)
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h1 class="h2 pt-3 pb-2 mb-3 border-bottom">Gestionar Equipo: {{ $artista->nombre_artistico }}</h1>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Add Editor Form -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Invitar Nuevo Editor</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('artist.panel.team.store', $artista->artista_id) }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-auto flex-grow-1">
                            <label for="email" class="visually-hidden">Email del Usuario</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="email@usuario.com" required>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary mb-3">Añadir Editor</button>
                        </div>
                    </form>
                    <div class="form-text">El usuario debe estar registrado en la plataforma.</div>
                </div>
            </div>

            <!-- List Editors -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Editores Actuales</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($editors as $editor)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset($editor->avatar ?? 'multimedia/img/default-avatar.png') }}" class="rounded-circle me-3" width="40" height="40" style="object-fit:cover">
                                <div>
                                    <h6 class="mb-0">{{ $editor->nombre_usuario }} @if($editor->usuario_id === Auth::id()) <span class="badge bg-info text-dark">Tú</span> @endif</h6>
                                    <small class="text-muted">{{ $editor->email }}</small>
                                </div>
                            </div>
                            
                            @if($editor->usuario_id !== Auth::id())
                                <form action="{{ route('artist.panel.team.destroy', [$artista->artista_id, $editor->usuario_id]) }}" method="POST" onsubmit="return confirm('¿Quitar acceso a este editor?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
