<?php

namespace Database\Seeders;

use App\Helpers\Functions;
use App\Models\CursoPlataforma;
use Illuminate\Database\Seeder;
use App\Models\CoursePlatformVideo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CoursePlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $courses = [
            [
                'name' => 'Entrenamiento Básico para Perros',
                'description' => 'Un curso completo que enseña las habilidades básicas de obediencia para perros. Ideal para dueños primerizos.',
                'image' => 'images/cursos_plataforma/dog-trainer-interacting-with-their-pet.jpg', // Asegúrate de que la imagen esté en la ruta correcta
                'duration' => '40',
                'price' => 49.99,
                'currency_id' => 1,
                'difficulty' => 1,
                'videos' => [
                    [
                        'url' => 'https://www.youtube.com/watch?v=5qFTKHGO4ws',
                        'video' => 'https://www.youtube.com/watch?v=5qFTKHGO4ws',
                        'title' => 'Cómo ENTRENAR a tu PERRO PASO a PASO (Todas las razas)',
                        'duration' => '00:07:38',
                        'thumbnail' => 'thumbnails/cursos_plataforma/seguir_la_pelota.png',
                    ],
                    [
                        'url' => 'https://www.youtube.com/watch?v=z1cz4c36w_o',
                        'video' => 'https://www.youtube.com/watch?v=z1cz4c36w_o',
                        'title' => 'La PRIMERA ORDEN que DEBES ENSEÑAR a un PERRO - MartGon',
                        'duration' => '00:08:26',
                        'thumbnail' => 'thumbnails/cursos_plataforma/saludar.png',
                    ],
                ]
            ],
            [
                'name' => 'Cuidado y Bienestar de Mascotas',
                'description' => 'Aprende sobre la salud y el bienestar de tus mascotas, incluyendo nutrición, ejercicio y cuidados generales.',
                'image' => 'images/cursos_plataforma/close-up-veterinarian-checking-dog.jpg', // Asegúrate de que la imagen esté en la ruta correcta
                'duration' => '20',
                'price' => 59.99,
                'currency_id' => 1,
                'difficulty' => 2,
                'videos' => [
                    [
                        'url' => 'https://www.youtube.com/watch?v=2nAdPho0Nw4',
                        'video' => 'https://www.youtube.com/watch?v=2nAdPho0Nw4',
                        'title' => 'Cuidados necesarios para mantener la salud y el bienestar de las mascotas',
                        'duration' => '00:03:23',
                        'thumbnail' => 'thumbnails/cursos_plataforma/chequeo_general.png',
                    ],
                    [
                        'url' => 'https://www.youtube.com/watch?v=aZSDuIzog-Q&pp=ygUfQ3VpZGFkbyB5IEJpZW5lc3RhciBkZSBNYXNjb3Rhcw%3D%3D',
                        'video' => 'https://www.youtube.com/watch?v=aZSDuIzog-Q&pp=ygUfQ3VpZGFkbyB5IEJpZW5lc3RhciBkZSBNYXNjb3Rhcw%3D%3D',
                        'title' => 'Los 5 dominios de bienestar animal',
                        'duration' => '00:01:33',
                        'thumbnail' => 'thumbnails/cursos_plataforma/corte_pelo.png',
                    ],
                ]
            ],
        ];

        foreach ($courses as $course) {
            $courseDuration = 0;

            // Recorrer cada video del curso
            foreach ($course['videos'] as &$video) {
                $videoName = basename($video['video']);
                $duration = $video['duration'];

                // Sumar la duración del video a la duración total del curso
                list($hours, $minutes, $seconds) = explode(':', $duration);
                $courseDuration += ($hours * 3600) + ($minutes * 60) + $seconds;
            }

            // Convertir la duración total a formato H:i:s
            $courseDurationFormat = gmdate("H:i:s", $courseDuration);

            // Asignar la duración total al curso
            $course['duration'] = $courseDurationFormat;
            $coursePlatform = CursoPlataforma::create([
                'name' => $course['name'],
                'description' => $course['description'],
                'difficulty' => $course['difficulty'],
                'image' => $course['image'],
                'duration' => $course['duration'],
                'price' => $course['price']
            ]);
               // Agregar los videos relacionados al curso
               foreach ($course['videos'] as $video) {
                CoursePlatformVideo::create(array_merge($video, ['course_platform_id' => $coursePlatform->id]));
            }
        }
    }
}
