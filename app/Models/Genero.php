<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    use HasFactory;

    protected $table = 'genero';
    protected $primaryKey = 'genero_id';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function artistas()
    {
        return $this->belongsToMany(Artista::class, 'artista_genero', 'genero_id', 'artista_id');
    }
}
