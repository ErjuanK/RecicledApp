@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header Profile Card -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                        <div class="position-relative">
                            <img src="{{ asset($user->avatar ?? 'multimedia/img/default-avatar.png') }}" 
                                 alt="Avatar de {{ $user->nombre_usuario }}" 
                                 class="rounded-circle shadow"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                        
                        <div class="text-center text-md-start flex-grow-1">
                            <h1 class="fw-bold mb-1">{{ $user->nombre_usuario }}</h1>
                            @if(Auth::id() === $user->usuario_id)
                                <p class="text-muted mb-2">{{ $user->email }}</p>
                            @endif
                            
                            @if($user->ciudad || $user->pais)
                                <p class="text-muted mb-3">
                                    <i class="fa-solid fa-location-dot me-1"></i>
                                    {{ implode(', ', array_filter([$user->ciudad, $user->pais])) }}
                                </p>
                            @endif
                            
                            <!-- Badges / Roles could go here -->
                            @if($user->rol === 'editor' || $user->rol === 'admin')
                                <span class="badge bg-purple text-white">Editor</span>
                            @endif
                        </div>

                        <div class="d-flex flex-column gap-2">
                             @if(Auth::id() === $user->usuario_id)
                                <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-pen"></i> Editar Perfil
                                </a>
                                @if($user->rol === 'editor' || $user->rol === 'admin')
                                    <a href="{{ route('artist.panel') }}" class="btn btn-primary">
                                        <i class="fa-solid fa-microphone"></i> Panel Artista
                                    </a>
                                @endif
                             @endif
                             <!-- Follow Button Placeholder -->
                             <!-- <button class="btn btn-primary">Seguir</button> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Main Content: Anotaciones/Activity -->
                <div class="col-lg-8">
                    <div class="mb-4">
                        <h3 class="border-bottom pb-2 mb-3">Mis Anotaciones</h3>
                        
                        @forelse($anotaciones as $nota)
                            <div class="card mb-3 border-start border-4 border-primary shadow-sm h-100">
                                <div class="card-body">
                                    <p class="small text-muted mb-1">
                                        Anotación en <span class="fw-bold text-primary">{{ $nota->letra->cancion->titulo }}</span> 
                                        de <span class="fw-bold text-primary">{{ $nota->letra->cancion->artista->nombre_artistico }}</span>
                                    </p>
                                    <blockquote class="blockquote mb-0 fs-6">
                                        {{ $nota->explicacion }}
                                    </blockquote>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 bg-light rounded text-muted">
                                <i class="fa-regular fa-comment-dots fa-3x mb-3"></i>
                                <p>Este usuario aún no ha realizado anotaciones.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Sidebar: Stats/Social -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white fw-bold">Estadísticas</div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Anotaciones
                                <span class="badge bg-primary rounded-pill">0</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Puntos Genius
                                <span class="badge bg-warning text-dark rounded-pill">0</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
