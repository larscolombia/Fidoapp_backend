<?php

namespace Modules\Category\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Category\Models\Category;

class CategoryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('IS_DUMMY_DATA')) {
            $data = [

                [
                    'slug' => 'general-veterinary-care',
                    'name' => 'Atención Veterinaria General',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/veterinary-cat/general_veterinary_care.png'),
                    'type' => 'veterinary'
                ],
                [
                    'slug' => 'emergency-and-critical-care',
                    'name' => 'Emergencia y Cuidados Críticos',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/veterinary-cat/emergency_and_critical_care.png'),
                    'type' => 'veterinary'
                ],
                [
                    'slug' => 'surgery',
                    'name' => 'Cirugía',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/veterinary-cat/surgery.png'),
                    'type' => 'veterinary'
                ],
                [
                    'slug' => 'dentistry',
                    'name' => 'Odontología',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/veterinary-cat/dentistry.png'),
                    'type' => 'veterinary'
                ],
                [
                    'slug' => 'ophthalmology',
                    'name' => 'Oftalmología',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/veterinary-cat/ophthalmology.png'),
                    'type' => 'veterinary'
                ],
                [
                    'slug' => 'cardiology',
                    'name' => 'Cardiología',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/veterinary-cat/cardiology.png'),
                    'type' => 'veterinary'
                ],
                [
                    'slug' => 'neurology',
                    'name' => 'Neurología',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/veterinary-cat/neurology.png'),
                    'type' => 'veterinary'
                ],
                [
                    'slug' => 'reproductive-medicine',
                    'name' => 'Medicina Reproductiva',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/veterinary-cat/reproductive_medicine.png'),
                    'type' => 'veterinary'
                ],
                [
                    'slug' => 'radiology-and-imaging',
                    'name' => 'Radiología e Imagenología',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/veterinary-cat/radiology_and_imaging.png'),
                    'type' => 'veterinary'
                ],






            ];
            foreach ($data as $key => $val) {
                // $subCategorys = $val['sub_category'];
                $featureImage = $val['feature_image'] ?? null;
                $categoryData = Arr::except($val, ['sub_category', 'feature_image']);
                $category = Category::create($categoryData);
                if (isset($featureImage)) {
                    $this->attachFeatureImage($category, $featureImage);
                }
                if (!empty($subCategorys)) {
                    foreach ($subCategorys as $subKey => $subCategory) {
                        $subCategory['parent_id'] = $category->id;
                        $featureImage = $subCategory['feature_image'] ?? null;
                        $sub_categoryData = Arr::except($subCategory, ['feature_image']);
                        $subcategoryData = Category::create($sub_categoryData);
                        if (isset($featureImage)) {
                            $this->attachFeatureImage($subcategoryData, $featureImage);
                        }
                    }
                }
            }
        }
    }

    private function attachFeatureImage($model, $publicPath)
    {
        if (!env('IS_DUMMY_DATA_IMAGE')) return false;

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('feature_image');

        return $media;
    }
}
