<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Configuración Legacy
    protected $table = 'usuario';
    protected $primaryKey = 'usuario_id';
    public $timestamps = false; // La tabla tiene fecha_registro pero no updated_at

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre_usuario',
        'email',
        'contrasena',
        'rol',
        'avatar',
        'nombre_real',
        'apellidos',
        'calle',
        'codigo_postal',
        'ciudad',
        'pais'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    /**
     * Get the password for the authentication.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    /**
     * Get the name of the password column.
     *
     * @return string
     */
    public function getAuthPasswordName()
    {
        return 'contrasena';
    }

    /**
     * Get the user's name attribute (maps to nombre_usuario for compatibility).
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->nombre_usuario;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_registro' => 'datetime',
            'contrasena' => 'hashed',
        ];
    }

    /**
     * Get the artists that the user manages.
     */
    public function artistas()
    {
        return $this->belongsToMany(Artista::class, 'artista_editor', 'usuario_id', 'artista_id')
                    ->withPivot('fecha_asignacion');
    }
}
