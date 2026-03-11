<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    /** @use HasFactory<\Database\Factories\AlbumFactory> */
    use HasFactory;

    protected $table = 'album';
    protected $primaryKey = 'album_id';
    public $timestamps = false;

    protected $fillable = [
        'artista_id',
        'titulo',
        'fecha_lanzamiento',
        'contexto',
        'portada_url',
        'estado'
    ];

    /**
     * Get the artist that owns the album.
     */
    public function artista()
    {
        return $this->belongsTo(Artista::class, 'artista_id', 'artista_id');
    }

    /**
     * Get the songs for the album.
     */
    public function canciones()
    {
        return $this->hasMany(Cancion::class, 'album_id', 'album_id');
    }
}
