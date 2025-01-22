<?php

namespace Database\Seeders;

use App\Models\Comando;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ComandosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $comandos = [
            [
                'name' => 'saltar',
                'description' => 'Instrucción para indicar salto',
                'type' => 'basico',
                'is_favorite' => false,
                'category_id' => 1,
                'pet_id' => 3,
                'voz_comando' => 'Saltar',
                'instructions' => 'Sentar a la mascota e indicarle que debe hacer un salto.'
            ],
            [
                'name' => 'sentar',
                'description' => 'Instrucción para que la mascota se siente',
                'type' => 'basico',
                'is_favorite' => true,
                'category_id' => 1,
                'pet_id' => 1,
                'voz_comando' => 'Sentar',
                'instructions' => 'Levantar la mano con una golosina y decir "sentar".'
            ],
            [
                'name' => 'venir',
                'description' => 'Instrucción para que la mascota venga hacia ti',
                'type' => 'basico',
                'is_favorite' => false,
                'category_id' => 2,
                'pet_id' => 2,
                'voz_comando' => 'Venir',
                'instructions' => 'Llamar a la mascota con entusiasmo y mostrarle una golosina.'
            ],
            [
                'name' => 'quieto',
                'description' => 'Instrucción para que la mascota permanezca quieta',
                'type' => 'especializado',
                'is_favorite' => true,
                'category_id' => 2,
                'pet_id' => 3,
                'voz_comando' => 'Quieto',
                'instructions' => 'Decir "quieto" y no moverse hasta que la mascota se quede en su lugar.'
            ],
            [
                'name' => 'rodar',
                'description' => 'Instrucción para que la mascota ruede',
                'type' => 'especializado',
                'is_favorite' => false,
                'category_id' => 3,
                'pet_id' => 4,
                'voz_comando' => 'Rodar',
                'instructions' => 'Hacer que la mascota se acueste y luego decir "rodar".'
            ],
        ];
        foreach($comandos as $comando)
        {
            Comando::create($comando);
        }
    }
}
