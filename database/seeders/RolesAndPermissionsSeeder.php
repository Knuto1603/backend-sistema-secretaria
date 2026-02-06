<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Definimos el guard por defecto para evitar confusiones entre web y api
        $guardName = 'web';

        // 1. CREAR PERMISOS (Usamos firstOrCreate para evitar duplicados)
        // Proyecto A: Registro
        $pCrearSolicitudes = Permission::firstOrCreate(['name' => 'crear solicitudes', 'guard_name' => $guardName]);
        $pGestionarSolicitudes = Permission::firstOrCreate(['name' => 'gestionar solicitudes', 'guard_name' => $guardName]);

        // Proyecto B: Chatbot
        $pConfigurarBot = Permission::firstOrCreate(['name' => 'configurar chatbot', 'guard_name' => $guardName]);

        // Proyecto C: Analítica
        $pVerAnaliticas = Permission::firstOrCreate(['name' => 'ver analiticas', 'guard_name' => $guardName]);

        // 2. CREAR ROLES Y ASIGNAR PERMISOS

        // Rol Estudiante
        $roleEstudiante = Role::firstOrCreate(['name' => 'estudiante', 'guard_name' => $guardName]);
        $roleEstudiante->syncPermissions([$pCrearSolicitudes]);

        // Rol Secretaria (Operativo)
        $roleSecretaria = Role::firstOrCreate(['name' => 'secretaria', 'guard_name' => $guardName]);
        $roleSecretaria->syncPermissions([
            $pGestionarSolicitudes,
            $pConfigurarBot
        ]);

        // Rol Secretario Académico (Gestión y Supervisión)
        $roleSecretarioAcademico = Role::firstOrCreate(['name' => 'secretario academico', 'guard_name' => $guardName]);
        $roleSecretarioAcademico->syncPermissions([
            $pGestionarSolicitudes,
            $pConfigurarBot,
            $pVerAnaliticas
        ]);

        // Rol Decano (Alta Dirección)
        $roleDecano = Role::firstOrCreate(['name' => 'decano', 'guard_name' => $guardName]);
        $roleDecano->syncPermissions([
            $pVerAnaliticas
        ]);

        // Rol Admin (Tiene todo)
        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guardName]);

        // 3. CREAR USUARIOS DE PRUEBA
        // Usamos updateOrCreate para no duplicar usuarios si corres el seeder de nuevo

        $decano = User::updateOrCreate(
            ['email' => 'decano@universidad.edu'],
            [
                'name' => 'Usuario Decano',
                'password' => Hash::make('password123'),
            ]
        );
        $decano->assignRole($roleDecano);

        $secAcademico = User::updateOrCreate(
            ['email' => 'sec.academico@universidad.edu'],
            [
                'name' => 'Usuario Secretario Académico',
                'password' => Hash::make('password123'),
            ]
        );
        $secAcademico->assignRole($roleSecretarioAcademico);

        $secretaria = User::updateOrCreate(
            ['email' => 'secretaria@universidad.edu'],
            [
                'name' => 'Usuario Secretaria',
                'password' => Hash::make('password123'),
            ]
        );
        $secretaria->assignRole($roleSecretaria);

        $estudiante = User::updateOrCreate(
            ['email' => 'alumno@universidad.edu'],
            [
                'name' => 'Juan Alumno',
                'password' => Hash::make('password123'),
            ]
        );
        $estudiante->assignRole($roleEstudiante);
    }
}
