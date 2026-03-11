<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Artista extends Model
{
    /** @use HasFactory<\Database\Factories\ArtistaFactory> */
    use HasFactory;

    protected $table = 'artista';
    protected $primaryKey = 'artista_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre_artistico',
        'biografia',
        'foto_url'
    ];

    /**
     * Get the editors associated with the artist.
     */
    public function editores()
    {
        return $this->belongsToMany(User::class, 'artista_editor', 'artista_id', 'usuario_id')
                    ->withPivot('fecha_asignacion');
    }

    /**
     * Get the genres associated with the artist.
     */
    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'artista_genero', 'artista_id', 'genero_id');
    }

    /**
     * Get the albums for the artist.
     */
    public function albums()
    {
        return $this->hasMany(Album::class, 'artista_id', 'artista_id');
    }

    /**
     * Get all songs for the artist (including standalone).
     */
    public function canciones()
    {
        return $this->hasMany(Cancion::class, 'artista_id', 'artista_id');
    }
}
