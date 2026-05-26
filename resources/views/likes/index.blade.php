@extends('layouts.app')

@section('title', 'Mis Me Gustas')

@push('styles')
<style>
    body { background: #f8f5ff !important; }
    main { padding: 0 !important; background: #f8f5ff !important; }

    /* ── Página ── */
    .likes-page {
        min-height: calc(100vh - 130px);
        background: #f8f5ff;
        padding: 40px 5% 60px;
        font-family: 'Roboto', sans-serif;
    }

    /* ── Cabecera ── */
    .likes-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 48px;
    }
    .likes-icon {
        width: 54px; height: 54px;
        background: #f0e6ff;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        border: 1.5px solid #d8b4fe;
        flex-shrink: 0;
    }
    .likes-icon i { color: #7c3aed; font-size: 1.4rem; }
    .likes-header h1 {
        font-size: 2rem;
        font-weight: 900;
        color: #3b0764;
        letter-spacing: -0.5px;
    }
    .likes-header p {
        color: #9333ea;
        font-size: 0.88rem;
        margin-top: 2px;
        opacity: 0.7;
    }

    /* ── Sección ── */
    .likes-section { margin-bottom: 52px; }

    .likes-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.78rem;
        font-weight: 800;
        color: #7c3aed;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 2px solid #ede9fe;
    }
    .likes-section-title i { font-size: 1rem; color: #a855f7; }
    .count-badge {
        background: #ede9fe;
        color: #7c3aed;
        padding: 2px 10px;
        border-radius: 99px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0;
        border: 1px solid #ddd6fe;
    }

    /* ── Carrusel ── */
    .carousel-wrap {
        position: relative;
    }
    .carousel-track {
        display: flex;
        gap: 16px;
        overflow-x: auto;
        scroll-behavior: smooth;
        padding-bottom: 8px;
        scrollbar-width: none;         /* Firefox */
    }
    .carousel-track::-webkit-scrollbar { display: none; } /* Chrome */

    /* Flechas */
    .carousel-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        width: 40px; height: 40px;
        border-radius: 50%;
        border: 1.5px solid #ddd6fe;
        background: #fff;
        color: #7c3aed;
        font-size: 1rem;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(124,58,237,0.12);
        transition: background 0.2s, border-color 0.2s, transform 0.2s;
    }
    .carousel-arrow:hover {
        background: #7c3aed;
        color: #fff;
        border-color: #7c3aed;
    }
    .carousel-arrow-left  { left: -20px; }
    .carousel-arrow-right { right: -20px; }
    .carousel-arrow.hidden { opacity: 0; pointer-events: none; }

    /* ── Cards ── */
    .like-card {
        position: relative;
        flex-shrink: 0;
        width: 168px;
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        border: 1.5px solid #ede9fe;
        box-shadow: 0 2px 12px rgba(124,58,237,0.07);
        transition: transform 0.22s, box-shadow 0.22s, border-color 0.22s;
        cursor: default;
    }
    .like-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(124,58,237,0.18);
        border-color: #c4b5fd;
    }

    /* Portada */
    .like-card-cover {
        position: relative;
        width: 100%;
        aspect-ratio: 1/1;
        overflow: hidden;
        background: #f0e6ff;
    }
    .like-card-cover img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.35s;
    }
    .like-card:hover .like-card-cover img { transform: scale(1.05); }

    /* Overlay oscuro sutil al hover */
    .like-card-cover::after {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(59,7,100,0);
        transition: background 0.22s;
    }
    .like-card:hover .like-card-cover::after {
        background: rgba(59,7,100,0.15);
    }

    /* Botón X — aparece en hover */
    .like-card-remove {
        position: absolute;
        top: 8px; right: 8px;
        z-index: 20;
        width: 28px; height: 28px;
        border-radius: 50%;
        background: rgba(255,255,255,0.92);
        border: 1.5px solid #f0e6ff;
        color: #7c3aed;
        font-size: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        opacity: 0;
        transform: scale(0.8);
        transition: opacity 0.18s, transform 0.18s, background 0.15s, color 0.15s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .like-card:hover .like-card-remove {
        opacity: 1;
        transform: scale(1);
    }
    .like-card-remove:hover {
        background: #f87171;
        color: #fff;
        border-color: #f87171;
    }



    /* Cuerpo de la card */
    .like-card-body {
        padding: 10px 12px 14px;
        background: #fff;
    }
    .like-card-name {
        font-size: 0.88rem;
        font-weight: 700;
        color: #2d1060;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.3;
    }
    .like-card-sub {
        font-size: 0.74rem;
        color: #9333ea;
        margin-top: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        opacity: 0.75;
    }

    /* Artista: foto circular */
    .artist-cover img {
        border-radius: 50%;
        width: calc(100% - 28px);
        height: calc(100% - 28px);
        margin: 14px;
        object-fit: cover;
    }
    .artist-cover::after { display: none; }

    /* ── Estado vacío ── */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 40px 24px;
        background: #fff;
        border-radius: 20px;
        border: 1.5px dashed #ddd6fe;
        color: #c4b5fd;
        width: 100%;
    }
    .empty-state i { font-size: 2.2rem; }
    .empty-state p { font-size: 0.88rem; color: #a78bfa; text-align: center; margin: 0; }
    .empty-state a {
        margin-top: 4px;
        color: #7c3aed;
        font-weight: 700;
        font-size: 0.85rem;
        text-decoration: none;
    }
    .empty-state a:hover { text-decoration: underline; }
</style>
@endpush

@section('content')
<div class="likes-page">

    <!-- ── Cabecera ── -->
    <div class="likes-header">
        <div class="likes-icon">
            <i class="fa-solid fa-heart"></i>
        </div>
        <div>
            <h1>Mis Me Gustas</h1>
            <p>{{ $songs->count() + $albums->count() + $artists->count() }} elementos guardados</p>
        </div>
        <div style="margin-left:auto;">
            <button id="btn-import-playlist" class="px-4 py-2 bg-purple-600 text-white rounded-full hover:bg-purple-700 transition-colors">Importar Playlist</button>
        </div>
    </div>

    <!-- ══════════ CANCIONES ══════════ -->
    <div class="likes-section">
        <div class="likes-section-title">
            <i class="fa-solid fa-music"></i>
            Canciones
            <span class="count-badge">{{ $songs->count() }}</span>
        </div>

        @if($songs->isEmpty())
            <div class="carousel-track">
                <div class="empty-state">
                    <i class="fa-solid fa-music"></i>
                    <p>Todavía no has guardado ninguna canción.</p>
                    <a href="{{ route('foryou') }}">¡Desliza en Para Ti →</a>
                </div>
            </div>
        @else
            <div class="carousel-wrap" data-carousel="songs">
                <button class="carousel-arrow carousel-arrow-left hidden" onclick="scrollCarousel('songs', -1)">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="carousel-track" id="track-songs" onscroll="updateArrows('songs')">
                    @foreach($songs as $song)
                    <div class="like-card">
                        <div class="like-card-cover">
                            <img src="{{ $song->image_url ?: '' }}"
                                 alt="{{ $song->name }}"
                                 onerror="this.src='https://placehold.co/168x168/ede9fe/7c3aed?text=♪'">

                            <!-- X para eliminar -->
                            <form method="POST" action="{{ route('likes.destroy', $song->id) }}" style="display:contents">
                                @csrf @method('DELETE')
                                <button type="submit" class="like-card-remove" title="Quitar me gusta">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>


                        </div>
                        <div class="like-card-body">
                            <div class="like-card-name">{{ $song->name }}</div>
                            <div class="like-card-sub">{{ $song->artist_name ?? '—' }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button class="carousel-arrow carousel-arrow-right" onclick="scrollCarousel('songs', 1)">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- ══════════ ÁLBUMES ══════════ -->
    <div class="likes-section">
        <div class="likes-section-title">
            <i class="fa-solid fa-record-vinyl"></i>
            Álbumes
            <span class="count-badge">{{ $albums->count() }}</span>
        </div>

        @if($albums->isEmpty())
            <div class="carousel-track">
                <div class="empty-state">
                    <i class="fa-solid fa-record-vinyl"></i>
                    <p>Aún no has guardado ningún álbum.</p>
                </div>
            </div>
        @else
            <div class="carousel-wrap" data-carousel="albums">
                <button class="carousel-arrow carousel-arrow-left hidden" onclick="scrollCarousel('albums', -1)">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="carousel-track" id="track-albums" onscroll="updateArrows('albums')">
                    @foreach($albums as $album)
                    <div class="like-card">
                        <div class="like-card-cover">
                            <img src="{{ $album->image_url ?: '' }}"
                                 alt="{{ $album->name }}"
                                 onerror="this.src='https://placehold.co/168x168/ede9fe/7c3aed?text=LP'">
                            <form method="POST" action="{{ route('likes.destroy', $album->id) }}" style="display:contents">
                                @csrf @method('DELETE')
                                <button type="submit" class="like-card-remove" title="Quitar me gusta">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>
                        <div class="like-card-body">
                            <div class="like-card-name">{{ $album->name }}</div>
                            <div class="like-card-sub">{{ $album->artist_name ?? '' }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button class="carousel-arrow carousel-arrow-right" onclick="scrollCarousel('albums', 1)">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- ══════════ ARTISTAS ══════════ -->
    <div class="likes-section">
        <div class="likes-section-title">
            <i class="fa-solid fa-star"></i>
            Artistas
            <span class="count-badge">{{ $artists->count() }}</span>
        </div>

        @if($artists->isEmpty())
            <div class="carousel-track">
                <div class="empty-state">
                    <i class="fa-solid fa-star"></i>
                    <p>Todavía no sigues a ningún artista.</p>
                </div>
            </div>
        @else
            <div class="carousel-wrap" data-carousel="artists">
                <button class="carousel-arrow carousel-arrow-left hidden" onclick="scrollCarousel('artists', -1)">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="carousel-track" id="track-artists" onscroll="updateArrows('artists')">
                    @foreach($artists as $artist)
                    <div class="like-card">
                        <div class="like-card-cover artist-cover">
                            <img src="{{ $artist->image_url ?: '' }}"
                                 alt="{{ $artist->name }}"
                                 onerror="this.src='https://placehold.co/168x168/ede9fe/7c3aed?text=🎤'">
                            <form method="POST" action="{{ route('likes.destroy', $artist->id) }}" style="display:contents">
                                @csrf @method('DELETE')
                                <button type="submit" class="like-card-remove" title="Quitar me gusta">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </form>
                        </div>
                        <div class="like-card-body">
                            <div class="like-card-name">{{ $artist->name }}</div>
                            <div class="like-card-sub">Artista</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button class="carousel-arrow carousel-arrow-right" onclick="scrollCarousel('artists', 1)">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        @endif
    </div>

</div>

<audio id="preview-audio" style="display:none;"></audio>
{{-- Import modal --}}
<div id="import-modal" style="display:none; position:fixed; inset:0; z-index:60; align-items:center; justify-content:center;">
    <div style="position:absolute; inset:0; background:rgba(0,0,0,0.4);"></div>
    <div style="background:#fff; width:720px; max-width:94%; border-radius:12px; padding:24px; position:relative; z-index:62; box-shadow:0 18px 40px rgba(16,24,40,0.5);">
        <h3 style="margin:0 0 8px; font-weight:800; color:#2d1060; font-size:1.25rem;">Importar Playlist</h3>
        <p style="margin:0 0 12px; color:#6b21a8; font-size:0.95rem;">Pega aquí una lista de canciones (máximo 100). Formato: "Canción - Artista" (una por línea)</p>
        <div style="background:#f3e8ff; border:1px solid #ddd6fe; border-radius:8px; padding:10px; margin-bottom:12px; font-size:0.85rem; color:#5b21b6;">
            <strong>Ejemplos válidos:</strong><br>
            Shape of You - Ed Sheeran<br>
            Blinding Lights | The Weeknd
        </div>
        <textarea id="import-text" placeholder="Pega tus canciones aquí..." style="width:100%; min-height:200px; padding:12px; border:1.5px solid #e9d5ff; border-radius:8px; resize:vertical; font-family:monospace; font-size:0.9rem;"></textarea>
        <div style="margin-top:8px; color:#9333ea; font-size:0.85rem;">
            <span id="import-count">0</span> / 100 canciones
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:16px;">
            <button id="import-cancel" class="px-4 py-2 rounded-full border border-gray-300 hover:bg-gray-50 transition-colors">Cerrar</button>
            <button id="import-submit" class="px-4 py-2 bg-purple-600 text-white rounded-full hover:bg-purple-700 transition-colors font-semibold">Importar</button>
        </div>
        <div id="import-status" style="margin-top:14px; padding:10px; border-radius:6px; display:none; font-size:0.95rem;"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ── Carrusel ── */
const SCROLL_AMOUNT = 380;

function scrollCarousel(id, dir) {
    const track = document.getElementById('track-' + id);
    track.scrollLeft += dir * SCROLL_AMOUNT;
}

function updateArrows(id) {
    const track = document.getElementById('track-' + id);
    const wrap  = track.closest('.carousel-wrap');
    const left  = wrap.querySelector('.carousel-arrow-left');
    const right = wrap.querySelector('.carousel-arrow-right');
    left.classList.toggle('hidden',  track.scrollLeft <= 4);
    right.classList.toggle('hidden', track.scrollLeft + track.clientWidth >= track.scrollWidth - 4);
}

// Inicializar flechas al cargar
document.querySelectorAll('.carousel-track').forEach(track => {
    const id = track.id?.replace('track-', '');
    if (id) updateArrows(id);
});


</script>
<script>
// Import modal logic
const btnImport = document.getElementById('btn-import-playlist');
const modal = document.getElementById('import-modal');
const btnCancel = document.getElementById('import-cancel');
const btnSubmit = document.getElementById('import-submit');
const textarea = document.getElementById('import-text');
const statusEl = document.getElementById('import-status');
const countEl = document.getElementById('import-count');

// Contador de canciones en tiempo real
textarea.addEventListener('input', () => {
    const text = textarea.value.trim();
    const lines = text ? text.split('\n') : [];
    const validLines = lines.filter(l => l.trim() !== '').length;
    
    countEl.textContent = validLines;
    countEl.style.color = validLines > 100 ? '#dc2626' : '#9333ea';
    
    if (validLines > 100) {
        btnSubmit.disabled = true;
        btnSubmit.style.opacity = '0.6';
        btnSubmit.title = 'Máximo 100 canciones';
    } else {
        btnSubmit.disabled = false;
        btnSubmit.style.opacity = '1';
        btnSubmit.title = '';
    }
});

if (btnImport) btnImport.addEventListener('click', () => {
    modal.style.display = 'flex';
    textarea.value = '';
    countEl.textContent = '0';
    statusEl.style.display = 'none';
    statusEl.textContent = '';
    btnSubmit.disabled = false;
    btnSubmit.style.opacity = '1';
    textarea.focus();
});

btnCancel.addEventListener('click', () => {
    modal.style.display = 'none';
});

btnSubmit.addEventListener('click', async () => {
    const text = textarea.value.trim();
    if (!text) {
        showStatus('Por favor, pega al menos una canción.', 'error');
        return;
    }
    
    const validLines = text.split('\n').filter(l => l.trim() !== '').length;
    if (validLines > 100) {
        showStatus(`Máximo 100 canciones. Tienes ${validLines}.`, 'error');
        return;
    }

    showStatus('Importando... por favor espera.', 'loading');
    btnSubmit.disabled = true;

    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const res = await fetch('{{ route("likes.import") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ text })
        });
        const data = await res.json();

        if (res.ok && data.success) {
            const msg = `✅ ¡Importación completada! 🎵 ${data.imported.songs} canción(es), 💿 ${data.imported.albums} álbum(es), 🎤 ${data.imported.artists} artista(s).`;
            showStatus(msg, 'success');
            setTimeout(() => location.reload(), 1500);
        } else if (res.status === 422) {
            showStatus(`❌ ${data.error}`, 'error');
        } else {
            showStatus(data.message || 'Error al importar.', 'error');
        }
    } catch (e) {
        console.error(e);
        showStatus('Error en la solicitud. Intenta de nuevo.', 'error');
    } finally {
        btnSubmit.disabled = false;
    }
});

function showStatus(msg, type) {
    statusEl.style.display = 'block';
    statusEl.textContent = msg;
    
    if (type === 'success') {
        statusEl.style.background = '#dcfce7';
        statusEl.style.color = '#15803d';
        statusEl.style.border = '1px solid #86efac';
    } else if (type === 'error') {
        statusEl.style.background = '#fee2e2';
        statusEl.style.color = '#991b1b';
        statusEl.style.border = '1px solid #fca5a5';
    } else {
        statusEl.style.background = '#fef3c7';
        statusEl.style.color = '#78350f';
        statusEl.style.border = '1px solid #fde68a';
    }
}
</script>
@endpush
