<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Create the admin user in the database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@genius.com'],
            [
                'nombre_usuario' => 'Administrador',
                'contrasena'     => Hash::make('Admin1234!'),
                'rol'            => 'admin',
                'nombre_real'    => 'Admin',
                'apellidos'      => 'Genius',
            ]
        );

        $this->command->info('✅ Usuario admin creado: admin@genius.com / Admin1234!');
    }
}
