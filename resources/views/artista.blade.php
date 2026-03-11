@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estilo-artista.css') }}">
    <style>
        /* Remove default main padding for full-width hero */
        main { padding: 0 !important; }
    </style>
@endpush

@section('content')
<div class="contenedor-hero-artista" style="width: 100vw; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw;">
    <div class="imagen-hero" style="background-image: url('{{ $artista->imagen_hero }}');">
        <div class="superposicion-hero"></div>
    </div>
</div>

<div class="contenedor-principal-artista">
    
    <aside class="barra-lateral-artista">
        <div class="contenedor-imagen-perfil">
            <img src="{{ $artista->imagen_perfil }}" alt="{{ $artista->nombre_artistico }}" class="imagen-perfil">
        </div>
        
        <h1 class="nombre-artista">{{ $artista->nombre_artistico }}</h1>
        
        <div class="seccion-sobre-artista">
            <h3 class="titulo-sobre">Biografía</h3>
            
            <div class="texto-biografia-artista">
                @php
                $biografia = $artista->biografia;
                $limite = 200;
                @endphp
                
                @if(strlen($biografia) > $limite)
                    @php
                    $corte = strpos($biografia, ' ', $limite);
                    if ($corte === false) $corte = $limite;
                    $visible = substr($biografia, 0, $corte);
                    $resto = substr($biografia, $corte);
                    @endphp
                    <span class="biografia-visible">{!! nl2br(e($visible)) !!}...</span>
                    <span class="biografia-oculta" style="display:none;">{!! nl2br(e($resto)) !!}</span>
                    <a href="#" class="enlace-leer-mas" onclick="toggleBiografia(event)">seguir leyendo</a>
                @else
                    {!! nl2br(e($biografia)) !!}
                @endif
            </div>
            
            @if(!empty($artista->generos))
            <div class="generos-artista">
                @foreach(array_slice($artista->generos, 0, 5) as $genero)
                    <span class="etiqueta-genero">{{ ucfirst($genero) }}</span>
                @endforeach
            </div>
            @endif
        </div>
    </aside>
    
    <main class="contenido-artista">
        
        <section class="seccion-contenido">
            <h2 class="titulo-seccion" style="color: #6F00D0; font-size: 1.5em;">CANCIONES POPULARES DE {{ strtoupper($artista->nombre_artistico) }}</h2>
            
            <div class="cuadricula-canciones">
                @foreach(array_slice($artista->canciones_populares, 0, 8) as $cancion)
                <a href="{{ route('cancion.show', $cancion->id) }}" class="tarjeta-cancion">
                    <div class="imagen-tarjeta-cancion">
                        <img src="{{ $cancion->miniatura_url }}" alt="Cover">
                    </div>
                    <div class="info-tarjeta-cancion">
                        <h3 class="titulo-tarjeta-cancion">{{ $cancion->titulo }}</h3>
                        <p class="artista-tarjeta-cancion">{{ $cancion->artista_nombre }}</p>
                        <div class="vistas-tarjeta-cancion">
                            <i class="fa-regular fa-eye"></i> {{ $cancion->vistas_formateadas }}
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        
        <section class="seccion-contenido">
            <div class="contenedor-busqueda-completo">
                <i class="fa-solid fa-magnifying-glass icono-busqueda"></i>
                <input type="text" id="buscador-canciones-artista" placeholder="Ver todas las canciones de {{ $artista->nombre_artistico }}" style="color: #6F00D0;">
            </div>
            <div id="resultados-busqueda-artista" class="resultados-busqueda"></div>
        </section>
        
        <section class="seccion-contenido">
            <h2 class="titulo-seccion" style="color: #6F00D0; font-size: 1.5em;">ÁLBUMES</h2>
            <div class="cuadricula-albumes-creativa">
                @foreach($artista->albumes as $index => $album)
                    <a href="{{ route('album.show', $album->id) }}" class="elemento-album item-{{ ($index % 6) + 1 }}">
                        <div class="envoltura-portada-album">
                            <img src="{{ $album->portada_url }}" alt="{{ $album->nombre_album }}">
                            <div class="info-superpuesta-album">
                                <span class="anio-album">{{ $album->anio }}</span>
                            </div>
                        </div>
                        <div class="titulo-album-simple">{{ $album->nombre_album }}</div>
                    </a>
                @endforeach
            </div>
        </section>
        
    </main>
</div>

<script type="application/json" id="datos-canciones-artista">
{!! json_encode($artista->todas_las_canciones) !!}
</script>

@endsection

@push('scripts')
<script src="{{ asset('js/busqueda-artista.js') }}"></script>
<script>
function toggleBiografia(event) {
    event.preventDefault();
    const bioOculta = document.querySelector('.biografia-oculta');
    const enlace = event.target;
    
    if (bioOculta.style.display === 'none') {
        bioOculta.style.display = 'inline';
        enlace.textContent = 'mostrar menos';
    } else {
        bioOculta.style.display = 'none';
        enlace.textContent = 'seguir leyendo';
    }
}
</script>
@endpush
