<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExpiryDateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now(); // Obtiene la fecha y hora actual

        DB::table('expiry_dates')->insert([
            [
                'day' => '2',
                'expire_date_text' => '2 días',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'day' => '5',
                'expire_date_text' => '5 días',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'day' => '7',
                'expire_date_text' => '1 semana',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'day' => '14',
                'expire_date_text' => '2 semanas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
