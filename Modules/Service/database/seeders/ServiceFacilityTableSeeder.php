<?php

namespace Modules\Service\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Service\Models\ServiceFacility;

class ServiceFacilityTableSeeder extends Seeder
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
                [
                    'name' => 'Alimentación y Agua',
                    'slug' => 'Feeding and Watering',
                    'description' => 'Asegurarse de que las mascotas sean alimentadas de acuerdo a sus necesidades dietéticas y tengan acceso a agua fresca.',
                    'status' => 1,
                ],
                [
                    'name' => 'Supervisión 24/7',
                    'slug' => '24-7 Supervision',
                    'description' => 'Monitoreo continuo para garantizar la seguridad y el bienestar de todas las mascotas.',
                    'status' => 1,
                ],
                [
                    'name' => 'Cuidado Especial para Mascotas Mayores y con Necesidades Especiales',
                    'slug' => 'Special Care for Seniors and Special Needs Pets',
                    'description' => 'Atender los requisitos específicos de mascotas mayores o aquellas con condiciones médicas.',
                    'status' => 1,
                ],
                [
                    'name' => 'Áreas de Juego',
                    'slug' => 'Play Areas',
                    'description' => 'Áreas designadas o patios de juego donde las mascotas pueden ejercitarse e interactuar con otros animales compatibles.',
                    'status' => 1,
                ],
                [
                    'name' => 'Suites Privadas',
                    'slug' => 'Private Suites',
                    'description' => 'Ofrecer habitaciones o suites privadas y cómodas para mascotas que prefieren más soledad y un ambiente tranquilo.',
                    'status' => 1,
                ],
                [
                    'name' => 'Fotografía de Mascotas',
                    'slug' => 'Pet Photography',
                    'description' => 'Ofrecer sesiones de fotografía de mascotas y enviar fotos a los dueños como un recuerdo encantador.',
                    'status' => 1,
                ],
                [
                    'name' => 'Informes de Actividad',
                    'slug' => 'Report Cards',
                    'description' => "Proporcionar informes diarios o periódicos a los dueños de mascotas detallando las actividades y comportamientos de sus mascotas.",
                    'status' => 1,
                ],
            ];
            
            foreach ($data as $key => $value) {
                $service = [
                    'slug' => $value['slug'],
                    'name' => $value['name'],
                    'description' => $value['description'],
                    'status' => $value['status'],
                ];
                $service = ServiceFacility::create($service);
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
