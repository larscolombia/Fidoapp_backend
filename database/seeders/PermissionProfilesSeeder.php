<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now(); // Obtiene la fecha y hora actual

        DB::table('permission_profiles')->insert([
            [
                'description' => 'Administrador',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'description' => 'Visualizar Perfil',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'description' => 'Entramientos',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'description' => 'Datos Médicos',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
