<?php

namespace Modules\Service\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Service\Models\SystemService;

class SystemServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
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
                // Hair Category Services
                [
                    'slug' => 'boarding',
                    'type' => 'boarding',
                    'name' => 'boarding',
                    'description' => 'Alojamiento seguro y cómodo para mascotas, para unas vacaciones sin preocupaciones',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/system_service/boarding.png'),
                ],
                [
                    'slug' => 'veterinary',
                    'type' => 'veterinary',
                    'name' => 'veterinary',
                    'description' => 'Atención veterinaria integral para mascotas felices y saludables',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/system_service/veterinary.png'),
                ],
                [ 
                    'slug' => 'grooming',
                    'type' => 'grooming',
                    'name' => 'grooming',
                    'description' => 'Acicalamiento profesional para una apariencia fresca y fabulosa',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/system_service/grooming.png'),
                ],
                [
                    'slug' => 'walking',
                    'type' => 'walking',
                    'name' => 'walking',
                    'description' => 'Paseos enriquecedores para patas felices, amor y ejercicio',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/system_service/walking.png'),
                ],
                [
                    'slug' => 'training',
                    'type' => 'training',
                    'name' => 'training',
                    'description' => "Desata el potencial de tu mascota con entrenamiento y cuidado experto",
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/system_service/training.png'),
                ],
                [
                    'slug' => 'daycare',
                    'type' => 'daycare',
                    'name' => 'daycare',
                    'description' => 'Guardería cariñosa para mascotas felices, asegurando tiempo de juego y mimos',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/system_service/day_care.png'),
                ],
            ];
            
            foreach ($data as $key => $value) {
                $featureImage = $value['feature_image'] ?? null;
                $service = [
                    'slug' => $value['slug'],
                    'name' => $value['name'],
                    'description' => $value['description'],
                    'type' => $value['type'],
                    'status' => $value['status'],
                ];
                $service = SystemService::create($service);
                if (isset($featureImage)) {
                    $this->attachFeatureImage($service, $featureImage);
                }
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
