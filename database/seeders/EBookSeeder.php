<?php

namespace Database\Seeders;

use App\Models\EBook;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $eBooks = [
            [
                'title' => 'Entrenamiento Canino Positivo Para Principiantes',
                'author' => 'Juanito Mancilla',
                'url' => 'https://www.amazon.com/-/es/Juanito-Mancilla/dp/1989638945',
                'cover_image' => 'images/e_books/entrenamiento_canino_.jpg', // Asegúrate de que la imagen esté en la ruta correcta
                'description' => 'Presentando cómo puedes entrenar a tu perro de una manera amorosa y amable SIN causarle sufrimiento innecesario ni dolor.

¿Quieres educar un perro feliz Y bien entrenado? ¿Quieres hacerlo mientras construyes un vínculo duradero con tu perro? ¿Quieres hacer todo esto usando métodos modernos y amables que tanto tú como tu cachorro adorarán?

Estoy seguro que, así como cualquier otro amante de los perros, has respondido que sí a todas esas preguntas porque, como dueños y aficionados de los canes, ¡no queremos más que criar perros felices y saludables!',
            ],
            [
                'title' => 'Libro Del Cuidado De Mi Perro: Diario De Salud Para Perros',
                'author' => 'Moulin Ajr',
                'url' => 'https://www.amazon.com/-/es/Libro-Del-Cuidado-Mi-Perro/dp/B0933KLM7Z',
                'cover_image' => 'images/e_books/cuidado_del_perro.jpg', // Asegúrate de que la imagen esté en la ruta correcta
                'description' => 'Porque las mascotas son más felices en casa, este diario está dedicado a los dueños que contratan a una niñera para cuidar de su mascota y de su casa mientras usted está fuera. Hay espacio para escribir paseos, golosinas, comidas, citas con el veterinario, etc., así que cuando vuelvas a casa, podrás ver fácilmente lo que la canguro escribió en el libro.',
            ],
        ];

        foreach ($eBooks as $eBook) {
            EBook::create($eBook);
        }
    }
}
