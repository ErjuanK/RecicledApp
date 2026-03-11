<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anotacion extends Model
{
    protected $table = 'anotacion';
    protected $primaryKey = 'anotacion_id';
    public $timestamps = false; // We use custom 'fecha_creacion' via DB default, or handle manually

    // If using 'fecha_creacion' manually
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = null;

    protected $fillable = [
        'letra_id', 
        'usuario_id', 
        'texto_seleccionado', 
        'explicacion', 
        'start_offset', 
        'end_offset', 
        'estado'
    ];

    public function letra()
    {
        return $this->belongsTo(Letra::class, 'letra_id', 'letra_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'usuario_id');
    }
}
