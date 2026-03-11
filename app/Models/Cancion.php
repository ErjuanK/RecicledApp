<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cancion extends Model
{
    /** @use HasFactory<\Database\Factories\CancionFactory> */
    use HasFactory;

    protected $table = 'cancion';
    protected $primaryKey = 'cancion_id';
    public $timestamps = false;

    protected $fillable = [
        'album_id',
        'artista_id',
        'titulo',
        'duracion',
        'contexto',
        'creditos',
        'estado',
        'portada'
    ];

    /**
     * Get the artist that owns the song.
     */
    public function artista()
    {
        return $this->belongsTo(Artista::class, 'artista_id', 'artista_id');
    }

    /**
     * Get the album that owns the song.
     */
    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'album_id');
    }

    /**
     * Get the lyrics for the song.
     */
    public function letra()
    {
        return $this->hasOne(Letra::class, 'cancion_id', 'cancion_id');
    }
}
