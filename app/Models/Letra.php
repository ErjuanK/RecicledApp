<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Letra extends Model
{
    protected $table = 'letra';
    protected $primaryKey = 'letra_id';
    public $timestamps = false;

    protected $fillable = ['cancion_id', 'contenido'];

    public function cancion()
    {
        return $this->belongsTo(Cancion::class, 'cancion_id', 'cancion_id');
    }

    public function anotaciones()
    {
        return $this->hasMany(Anotacion::class, 'letra_id', 'letra_id');
    }
}
