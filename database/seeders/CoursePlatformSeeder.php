<?php

namespace Database\Seeders;

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
                'url' => 'https://ejemplo.com/curso-entrenamiento-basico',
                'image' => 'images/cursos_plataforma/dog-trainer-interacting-with-their-pet.jpg', // Asegúrate de que la imagen esté en la ruta correcta
                'duration' => '40',
                'price' => 49.99,
                'videos' => [
                    [
                        'url' => asset('videos/cursos_plataforma/clase_1.mp4'),
                        'video' => 'videos/cursos_plataforma/clase_1.mp4',
                        'title' => 'Seguir la pelota',
                        'duration' => '6',
                        'thumbnail' => 'thumbnails/cursos_plataforma/seguir_la_pelota.png',
                    ],
                    [
                        'url' => asset('videos/cursos_plataforma/clase_2.mp4'),
                        'video' => 'videos/cursos_plataforma/clase_2.mp4',
                        'title' => 'Saludar',
                        'duration' => '8:00',
                        'thumbnail' => 'thumbnails/cursos_plataforma/saludar.png',
                    ],
                ]
            ],
            [
                'name' => 'Cuidado y Bienestar de Mascotas',
                'description' => 'Aprende sobre la salud y el bienestar de tus mascotas, incluyendo nutrición, ejercicio y cuidados generales.',
                'url' => 'https://ejemplo.com/curso-cuidado-bienestar',
                'image' => 'images/cursos_plataforma/close-up-veterinarian-checking-dog.jpg', // Asegúrate de que la imagen esté en la ruta correcta
                'duration' => '20',
                'price' => 59.99,
                'videos' => [
                    [
                        'url' => asset('videos/cursos_plataforma/clase_3.mp4'),
                        'video' => 'videos/cursos_plataforma/clase_3.mp4',
                        'title' => 'Chequeo general',
                        'duration' => '12:00',
                        'thumbnail' => 'thumbnails/cursos_plataforma/chequeo_general.png',
                    ],
                    [
                        'url' => asset('videos/cursos_plataforma/clase_4.mp4'),
                        'video' => 'videos/cursos_plataforma/clase_4.mp4',
                        'title' => 'Corte de pelo canino',
                        'duration' => '8:00',
                        'thumbnail' => 'thumbnails/cursos_plataforma/corte_pelo.png',
                    ],
                ]
            ],
        ];

        foreach ($courses as $course) {
            $coursePlatform = CursoPlataforma::create($course);
               // Agregar los videos relacionados al curso
               foreach ($course['videos'] as $video) {
                CoursePlatformVideo::create(array_merge($video, ['course_platform_id' => $coursePlatform->id]));
            }
        }
    }
}
