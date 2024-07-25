<?php

namespace Database\Seeders;

use App\Models\HerramientaType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HerramientaEntrenamientoTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        HerramientaType::create([
            'type' => 'Clicker',
            'icon' => 'fas fa-mouse-pointer',
        ]);

        HerramientaType::create([
            'type' => 'Silbato',
            'icon' => 'fas fa-whistle',
        ]);

        HerramientaType::create([
            'type' => 'Diarios',
            'icon' => 'fas fa-book-open',
        ]);
    }
}
