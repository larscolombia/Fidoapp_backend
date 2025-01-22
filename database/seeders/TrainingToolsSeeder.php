<?php

namespace Database\Seeders;

use App\Models\Herramienta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrainingToolsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $TrainingTools = [
            [
                'name' => 'Clicker',
                'description' => 'Herramienta básica para entrenamiento.',
                'type_id' => 1,
                'status' => 'activo',
                'audio' => 'audios/herramientas/click-4.mp3',
                'image' => 'images/herramientas/tecontador.webp',
            ],
            [
                'name' => 'Silbato',
                'description' => 'Herramienta básica para entrenamiento.',
                'type_id' => 2,
                'status' => 'activo',
                'audio' => 'audios/herramientas/silbato.mp3',
                'image' => 'images/herramientas/silbar.png',
            ],
            [
                'name' => 'Correa',
                'description' => 'Correa para paseos.',
                'type_id' => 3,
                'status' => 'activo',
                'audio' => 'audios/herramientas/leash.mp3',
                'image' => 'images/herramientas/Group.png',
            ],
        ];

        foreach($TrainingTools as $TrainingTool)
        {
            Herramienta::create($TrainingTool);
        }
    }
}
