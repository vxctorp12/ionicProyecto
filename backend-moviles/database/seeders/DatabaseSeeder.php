<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Asegurar que existan los roles y obtener sus IDs
        // Usamos updateOrInsert para no duplicar si ya ejecutaste el script SQL
        $roles = ['admin', 'docente', 'alumno'];
        
        foreach ($roles as $rol) {
            DB::table('roles')->updateOrInsert(
                ['nombre' => $rol],
                ['nombre' => $rol]
            );
        }

        $idAdmin = DB::table('roles')->where('nombre', 'admin')->value('id');
        $idDocente = DB::table('roles')->where('nombre', 'docente')->value('id');
        $idAlumno = DB::table('roles')->where('nombre', 'alumno')->value('id');

        // 2. Crear Usuarios de Prueba (con contraseña encriptada)
        
        // Usuario Administrador
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@escuela.edu'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'), // Contraseña encriptada
                'role_id' => $idAdmin,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Usuario Docente
        DB::table('users')->updateOrInsert(
            ['email' => 'docente@escuela.edu'],
            [
                'name' => 'Profesor Jirafales',
                'password' => Hash::make('password123'),
                'role_id' => $idDocente,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Usuario Alumno
        DB::table('users')->updateOrInsert(
            ['email' => 'alumno@escuela.edu'],
            [
                'name' => 'Chavo del 8',
                'password' => Hash::make('password123'),
                'role_id' => $idAlumno,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        echo "¡Datos de prueba cargados correctamente!\n";
    }
}