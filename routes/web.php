<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/registro-artista', [AuthController::class, 'showRegisterArtist'])->name('register.artist');
Route::post('/registro-artista', [AuthController::class, 'registerArtist'])->name('register.artist.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/profile', [App\Http\Controllers\UserController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [App\Http\Controllers\UserController::class, 'update'])->name('profile.update');

// Public Profile Routes
Route::get('/perfil', [App\Http\Controllers\UserController::class, 'dashboard'])->name('profile');
Route::get('/usuario/{username}', [App\Http\Controllers\UserController::class, 'show'])->name('user.show');

// Artist Panel - Selection Screen (List of Artists)
Route::get('/panel', [App\Http\Controllers\ArtistPanelController::class, 'index'])->name('artist.panel.index');

// Artist Panel - Specific Artist Management
Route::group(['prefix' => 'panel/artista/{id}', 'as' => 'artist.panel.'], function () {
    // Dashboard & Profile Update
    Route::get('/', [App\Http\Controllers\ArtistPanelController::class, 'manage'])->name('dashboard');
    Route::put('/', [App\Http\Controllers\ArtistPanelController::class, 'update'])->name('update');

    // Album Management
    Route::get('/album', [App\Http\Controllers\ArtistAlbumController::class, 'index'])->name('album.index');
    Route::get('/album/crear', [App\Http\Controllers\ArtistAlbumController::class, 'create'])->name('album.create');
    Route::post('/album', [App\Http\Controllers\ArtistAlbumController::class, 'store'])->name('album.store');
    Route::get('/album/{albumId}/editar', [App\Http\Controllers\ArtistAlbumController::class, 'edit'])->name('album.edit');
    Route::put('/album/{albumId}', [App\Http\Controllers\ArtistAlbumController::class, 'update'])->name('album.update');
    Route::delete('/album/{albumId}', [App\Http\Controllers\ArtistAlbumController::class, 'destroy'])->name('album.destroy');

    // Song Management
    Route::get('/album/{albumId}/cancion/crear', [App\Http\Controllers\ArtistSongController::class, 'create'])->name('song.create');
    Route::post('/album/{albumId}/cancion', [App\Http\Controllers\ArtistSongController::class, 'store'])->name('song.store');
    Route::get('/album/{albumId}/cancion/{cancionId}/editar', [App\Http\Controllers\ArtistSongController::class, 'edit'])->name('song.edit');
    Route::put('/album/{albumId}/cancion/{cancionId}', [App\Http\Controllers\ArtistSongController::class, 'update'])->name('song.update');
    Route::delete('/album/{albumId}/cancion/{cancionId}', [App\Http\Controllers\ArtistSongController::class, 'destroy'])->name('song.destroy');
    
    // Standalone Song Creation
    Route::get('/cancion/crear', [App\Http\Controllers\ArtistSongController::class, 'createStandalone'])->name('song.create.standalone');
    Route::post('/cancion', [App\Http\Controllers\ArtistSongController::class, 'storeStandalone'])->name('song.store.standalone');

    // Team Management
    Route::get('/equipo', [App\Http\Controllers\ArtistTeamController::class, 'index'])->name('team.index');
    Route::post('/equipo', [App\Http\Controllers\ArtistTeamController::class, 'store'])->name('team.store');
    Route::delete('/equipo/{userId}', [App\Http\Controllers\ArtistTeamController::class, 'destroy'])->name('team.destroy');
});
Route::get('/artista/{id}', [\App\Http\Controllers\ArtistaController::class, 'show'])->name('artista.show');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');

    // Album Management
    Route::get('/albums', [App\Http\Controllers\AdminController::class, 'albums'])->name('albums');
    Route::delete('/albums/{id}', [App\Http\Controllers\AdminController::class, 'destroyAlbum'])->name('albums.destroy');

    // Song Management
    Route::get('/canciones', [App\Http\Controllers\AdminController::class, 'canciones'])->name('canciones');
    Route::delete('/canciones/{id}', [App\Http\Controllers\AdminController::class, 'destroyCancion'])->name('canciones.destroy');

    // User Management
    Route::get('/usuarios', [App\Http\Controllers\AdminController::class, 'usuarios'])->name('usuarios');
    Route::delete('/usuarios/{id}', [App\Http\Controllers\AdminController::class, 'destroyUsuario'])->name('usuarios.destroy');
});

// Placeholder routes (to be implemented)
Route::get('/cancion/{id}', [\App\Http\Controllers\CancionController::class, 'show'])->name('cancion.show');
Route::get('/album/buscar/{artist}/{album}', [\App\Http\Controllers\AlbumController::class, 'showByItunes'])->name('album.itunes');
Route::get('/album/{id}', [\App\Http\Controllers\AlbumController::class, 'show'])->name('album.show');

// Search API
Route::get('/api/buscar', [\App\Http\Controllers\BusquedaController::class, 'buscar'])->name('api.buscar');

// Annotation API
Route::post('/api/anotacion', [\App\Http\Controllers\AnnotationController::class, 'store'])->name('api.anotacion.store');

// MusicDiscovery
Route::get('/discovery', [\App\Http\Controllers\DiscoveryController::class, 'showOnboarding'])->name('discovery');
Route::get('/discovery/genres', [\App\Http\Controllers\DiscoveryController::class, 'getAvailableGenres'])->name('discovery.genres');
Route::get('/discovery/search', [\App\Http\Controllers\DiscoveryController::class, 'searchSpotify'])->name('discovery.search');
Route::post('/discovery/generate', [\App\Http\Controllers\DiscoveryController::class, 'generateDashboard'])->name('discovery.generate');

// Lanzamientos
Route::get('/lanzamientos', [\App\Http\Controllers\ReleasesController::class, 'index'])->name('releases.index');

// For You (TikTok style discovery)
Route::middleware(['auth'])->group(function () {
    Route::get('/for-you', [\App\Http\Controllers\ForYouController::class, 'index'])->name('foryou');
    Route::get('/api/for-you/next', [\App\Http\Controllers\ForYouController::class, 'getNextTrack']);
    Route::post('/api/for-you/action', [\App\Http\Controllers\ForYouController::class, 'handleAction']);

    // Mis Me Gustas
    Route::get('/mis-megustas', [\App\Http\Controllers\LikesController::class, 'index'])->name('likes.index');
    Route::post('/api/likes/toggle', [\App\Http\Controllers\LikesController::class, 'toggle'])->name('likes.toggle');
    Route::get('/api/likes/check', [\App\Http\Controllers\LikesController::class, 'check'])->name('likes.check');
    Route::delete('/mis-megustas/{id}', [\App\Http\Controllers\LikesController::class, 'destroy'])->name('likes.destroy');
});

