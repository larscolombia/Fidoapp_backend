<?php

namespace Modules\Service\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Service\Models\ServiceTraining;

class ServiceTrainingTableSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks!
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        /*
         * Services Seed
         * ------------------
         */

        // DB::table('services')->truncate();
        // echo "Truncate: services \n";
        if (env('IS_DUMMY_DATA')) {
            $data = [
                [
                    'name' => 'Entrenamiento Básico de Obediencia',
                    'slug' => 'Basic Obedience Training',
                    'description' => "Enseñanza de comandos fundamentales como sentarse, quedarse, acostarse, venir y caminar al lado.",
                    'status' => 1,
                ],
                [
                    'name' => 'Entrenamiento de Cachorros',
                    'slug' => 'Puppy Training',
                    'description' => "Ejercicios de socialización, entrenamiento para hacer sus necesidades y comandos básicos adaptados para cachorros jóvenes.",
                    'status' => 1,
                ],
                [
                    'name' => 'Modificación de Comportamiento',
                    'slug' => 'Behavioral Modification',
                    'description' => "Abordar y corregir problemas de comportamiento como agresión, ansiedad o ladridos excesivos.",
                    'status' => 1,
                ],
                [
                    'name' => 'Entrenamiento para Perros de Servicio',
                    'slug' => 'Service Dog Training',
                    'description' => "Entrenamiento especializado para perros de servicio para realizar tareas para personas con discapacidades.",
                    'status' => 1,
                ],
                [
                    'name' => 'Manejo del Miedo y la Ansiedad',
                    'slug' => 'Fear and Anxiety Management',
                    'description' => "Técnicas para ayudar a las mascotas a afrontar situaciones que provocan miedo o ansiedad.",
                    'status' => 1,
                ],
                [
                    'name' => 'Entrenamiento de Llamada',
                    'slug' => 'Recall Training',
                    'description' => "Enfocado en enseñar a las mascotas a venir cuando se les llama, un comando esencial de seguridad.",
                    'status' => 1,
                ],
                [
                    'name' => 'Entrenamiento de Trucos',
                    'slug' => 'Trick Training',
                    'description' => "Enseñanza de trucos divertidos y entretenidos para impresionar a amigos y familiares.",
                    'status' => 1,
                ],
                [
                    'name' => 'Entrenamiento con Correa',
                    'slug' => 'Leash Training',
                    'description' => "Enseñar a las mascotas a caminar educadamente con una correa sin tirar.",
                    'status' => 1,
                ],
                [
                    'name' => 'Entrenamiento Avanzado de Obediencia',
                    'slug' => 'Advanced Obedience Training',
                    'description' => "Construyendo sobre comandos básicos, introduciendo señales más complejas y control sin correa.",
                    'status' => 1,
                ],
            ];
            
            foreach ($data as $key => $value) {
                $servicetraining = [
                    'name' => $value['name'],
                    'slug' => $value['slug'],
                    'description' => $value['description'],
                    'status' => $value['status'],
                ];
                $servicetraining = ServiceTraining::create($servicetraining);
            }
        }
        // Enable foreign key checks!
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function attachFeatureImage($model, $publicPath)
    {
        if(!env('IS_DUMMY_DATA_IMAGE')) return false;

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('feature_image');

        return $media;
    }
}
