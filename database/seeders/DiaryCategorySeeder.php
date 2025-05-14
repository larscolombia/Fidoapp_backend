<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DiaryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     public function run()
    {
        $categories = [
            [
                'name'        => 'Actividad',
                'slug'        => 'actividad',
                'parent_id'   => null,
                'status'      => 'active',
                'created_by'  => null,
                'updated_by'  => null,
                'deleted_by'  => null,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
                'deleted_at'  => null,
            ],
            [
                'name'        => 'Informe médico',
                'slug'        => 'informe-medico',
                'parent_id'   => null,
                'status'      => 'active',
                'created_by'  => null,
                'updated_by'  => null,
                'deleted_by'  => null,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
                'deleted_at'  => null,
            ],
            [
                'name'        => 'Entrenamiento',
                'slug'        => 'entrenamiento',
                'parent_id'   => null,
                'status'      => 'active',
                'created_by'  => null,
                'updated_by'  => null,
                'deleted_by'  => null,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
                'deleted_at'  => null,
            ],
        ];

        DB::table('diary_categories')->insert($categories);
    }
}
