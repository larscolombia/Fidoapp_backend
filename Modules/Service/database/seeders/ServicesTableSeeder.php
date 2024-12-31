<?php

namespace Modules\Service\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Category\Models\Category;
use Modules\Service\Models\Service;
use Modules\Service\Models\ServiceBranches;
use Modules\Service\Models\ServiceEmployee;

class ServicesTableSeeder extends Seeder
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

        if (env('IS_DUMMY_DATA')) {
            $data = [
                [
                    'slug' => 'routine-check-ups-and-examinations',
                    'name' => 'Chequeos y exámenes de rutina.',
                    'description' => 'Chequeos y exámenes de rutina.',
                    'duration_min' => 35,
                    'default_price' => 12.00,
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/veterinary-service/general_veterinary_care/routine_check_ups_and_examinations.png'),
                    'category' => 'general-veterinary-care',
                    'type' => 'veterinary',
                    'user_id' => [18, 19, 20, 21, 22, 23, 24],
                    // 'created_by' => 1,

                ],
                // [
                //     'slug' => 'vaccinations-and-preventive-care',
                //     'name' => 'Vaccinations and preventive care',
                //     'description' => 'Vaccinations and preventive care',
                //     'duration_min' => 25,
                //     'default_price' => 10.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/general_veterinary_care/vaccinations_and_preventive_care.png'),
                //     'category' => 'general-veterinary-care',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],
                // [
                //     'slug' => 'parasite-control-and-prevention',
                //     'name' => 'Parasite control and prevention',
                //     'description' => 'Parasite control and prevention',
                //     'duration_min' => 30,
                //     'default_price' => 16.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/general_veterinary_care/parasite_control_and_prevention.png'),
                //     'category' => 'general-veterinary-care',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],
                // [
                //     'slug' => 'nutrition-and-diet-counseling',
                //     'name' => 'Nutrition and diet counseling',
                //     'description' => 'Nutrition and diet counseling',
                //     'duration_min' => 20,
                //     'default_price' => 20.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/general_veterinary_care/nutrition_and_diet_counseling.png'),
                //     'category' => 'general-veterinary-care',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],


                // [
                //     'slug' => '24/7-emergency-services',
                //     'name' => '24/7 Emergency Services',
                //     'description' => '24/7 Emergency Services',
                //     'duration_min' => 40,
                //     'default_price' => 30.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/emergency_and_critical_care/24_7_emergency_services.png'),
                //     'category' => 'emergency-and-critical-care',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],
                // [
                //     'slug' => 'immediate-medical-attention-for-serious-injuries-or-illnesses',
                //     'name' => 'Immediate Medical Attention For Serious Injuries or Illnesses',
                //     'description' => 'Immediate medical attention for serious injuries or illnesses',
                //     'duration_min' => 56,
                //     'default_price' => 40.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/emergency_and_critical_care/immediate_medical_attention_for_serious_injuries_or_illnesses.png'),
                //     'category' => 'emergency-and-critical-care',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],
                // [
                //     'slug' => 'intensive-care-and-monitoring-for-critical_cases',
                //     'name' => 'intensive Care and Monitoring for Critical Cases',
                //     'description' => 'Intensive care and monitoring for critical cases',
                //     'duration_min' => 30,
                //     'default_price' => 35.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/emergency_and_critical_care/intensive_care_and_monitoring_for_critical_cases.png'),
                //     'category' => 'emergency-and-critical-care',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],


                // [
                //     'slug' => 'routine-spaying-and-neutering',
                //     'name' => 'Routine Spaying and Neutering',
                //     'description' => 'Routine spaying and neutering',
                //     'duration_min' => 20,
                //     'default_price' => 23.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/surgery/routine_spaying_and_neutering.png'),
                //     'category' => 'surgery',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],
                // [
                //     'slug' => 'soft-tissue-surgery',
                //     'name' => 'Soft Tissue Surgery',
                //     'description' => 'Soft tissue surgery',
                //     'duration_min' => 55,
                //     'default_price' => 35.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/surgery/soft_tissue_surgery.png'),
                //     'category' => 'surgery',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],
                // [
                //     'slug' => 'orthopedic-surgery',
                //     'name' => 'Orthopedic Surgery',
                //     'description' => 'Orthopedic surgery',
                //     'duration_min' => 50,
                //     'default_price' => 40.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/surgery/orthopedic_surgery.png'),
                //     'category' => 'surgery',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],

                // [
                //     'slug' => 'dental-check-ups-and-cleanings',
                //     'name' => 'Dental Check Ups and Cleanings',
                //     'description' => 'Dental check-ups and cleanings',
                //     'duration_min' => 10,
                //     'default_price' => 20.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/dentistry/dental_check_ups_and_cleanings.png'),
                //     'category' => 'dentistry',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],
                // [
                //     'slug' => 'tooth-extractions-and-oral-surgeries',
                //     'name' => 'Tooth Extractions and Oral Surgeries',
                //     'description' => 'Tooth extractions and oral surgeries',
                //     'duration_min' => 20,
                //     'default_price' => 30.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/dentistry/tooth_extractions_and_oral_surgeries.png'),
                //     'category' => 'dentistry',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],
                // [
                //     'slug' => 'dental-disease-treatment-and-prevention',
                //     'name' => 'Dental Disease Treatment and Prevention',
                //     'description' => 'Dental disease treatment and prevention',
                //     'duration_min' => 30,
                //     'default_price' => 40.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/dentistry/dental_disease_treatment_and_prevention.png'),
                //     'category' => 'dentistry',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],


                // [
                //     'slug' => 'eye-examinations-and-diagnostics',
                //     'name' => 'Eye Examinations and Diagnostics',
                //     'description' => 'Eye examinations and diagnostics',
                //     'duration_min' => 40,
                //     'default_price' => 25.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/ophthalmology/eye_examinations_and_diagnostics.png'),
                //     'category' => 'ophthalmology',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],
                // [
                //     'slug' => 'treatment-of-eye-infections-and-conditions',
                //     'name' => 'Treatment of Eye Infections and Conditions',
                //     'description' => 'Treatment of eye infections and conditions',
                //     'duration_min' => 25,
                //     'default_price' => 35.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/ophthalmology/treatment_of_eye_infections_and_conditions.png'),
                //     'category' => 'ophthalmology',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],



                // [
                //     'slug' => 'heart-health-assessment',
                //     'name' => 'Heart Health Assessment',
                //     'description' => 'Heart health assessment',
                //     'duration_min' => 15,
                //     'default_price' => 30.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/cardiology/heart_health_assessment.png'),
                //     'category' => 'cardiology',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],
                // [
                //     'slug' => 'diagnosis-and-management-of-heart-diseases',
                //     'name' => 'Diagnosis and Management of Heart Diseases',
                //     'description' => 'Diagnosis and management of heart diseases',
                //     'duration_min' => 25,
                //     'default_price' => 40.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/cardiology/diagnosis_and_management_of_heart_diseases.png'),
                //     'category' => 'cardiology',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],



                // [
                //     'slug' => 'diagnosis-and-treatment-of-neurological-disorders',
                //     'name' => 'Diagnosis and Treatment of Neurological Disorders',
                //     'description' => 'Diagnosis and treatment of neurological disorders',
                //     'duration_min' => 20,
                //     'default_price' => 16.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/neurology/diagnosis_and_treatment_of_neurological_disorders.png'),
                //     'category' => 'neurology',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],
                // [
                //     'slug' => 'seizure-management',
                //     'name' => 'Seizure Management',
                //     'description' => 'Seizure management',
                //     'duration_min' => 35,
                //     'default_price' => 10.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/neurology/seizure_management.png'),
                //     'category' => 'neurology',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],



                // [
                //     'slug' => 'breeding-management-and-fertility-testing',
                //     'name' => 'Breeding Management and Fertility Testing',
                //     'description' => 'Breeding management and fertility testing',
                //     'duration_min' => 45,
                //     'default_price' => 20.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/reproductive_medicine/breeding_management_and_fertility_testing.png'),
                //     'category' => 'reproductive-medicine',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],
                // [
                //     'slug' => 'pregnancy-and-birthing-assistance',
                //     'name' => 'Pregnancy and Birthing Assistance',
                //     'description' => 'Pregnancy and birthing assistance',
                //     'duration_min' => 25,
                //     'default_price' => 35.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/reproductive_medicine/pregnancy_and_birthing_assistance.png'),
                //     'category' => 'reproductive-medicine',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],


                // [
                //     'slug' => 'x-rays-and-ultrasounds-for-diagnostic-purposes',
                //     'name' => 'X-rays and Ultrasounds for Diagnostic Purposes',
                //     'description' => 'X-rays and ultrasounds for diagnostic purposes',
                //     'duration_min' => 40,
                //     'default_price' => 40.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/radiology_and_imaging/x-rays-and-ultrasounds-for-diagnostic-purposes.png'),
                //     'category' => 'radiology-and-imaging',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],
                // [
                //     'slug' => 'advanced-imaging',
                //     'name' => 'Advanced Imaging',
                //     'description' => 'Advanced imaging',
                //     'duration_min' => 20,
                //     'default_price' => 20.00,
                //     'status' => 1,
                //     'feature_image' => public_path('/dummy-images/veterinary-service/radiology_and_imaging/advanced-imaging.png'),
                //     'category' => 'radiology-and-imaging',
                //     'type' => 'veterinary',
                //     'user_id' => [18,19,20,21,22,23,24],
                //     // 'created_by' => 1,
                // ],


            ];
            foreach ($data as $key => $value) {
                $categroy = Category::where('slug', $value['category'])->first();
                $featureImage = $value['feature_image'] ?? null;
                $serviceData = Arr::except($value, ['sub_category', 'category', 'feature_image', 'user_id']);

                if (isset($sub_category)) {
                    $sub_category = Category::where('slug', $value['sub_category'])->first();
                }

                $service = [
                    'slug' => $value['slug'],
                    'name' => $value['name'],
                    'type' => $value['type'],
                    'category_id' => $categroy->id ?? null,
                    'sub_category_id' => $sub_category->id ?? null,
                    'description' => $value['description'],
                    'duration_min' => $value['duration_min'],
                    'default_price' => $value['default_price'],
                    'status' => $value['status'],
                ];
                $service = Service::create($service);
                if (isset($featureImage)) {
                    $this->attachFeatureImage($service, $featureImage);
                }
                ServiceBranches::create([
                    'service_id' => $service->id,
                    'branch_id' => get_pet_center_id(),
                    'service_price' => $service->default_price ?? 0,
                    'duration_min' => $service->duration_min,
                ]);
                foreach ($value['user_id'] as $key => $id) {
                    ServiceEmployee::create([
                        'service_id' => $service->id,
                        'employee_id' => $id,
                    ]);
                }
            }
        }
        // Enable foreign key checks!
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function attachFeatureImage($model, $publicPath)
    {
        if (!env('IS_DUMMY_DATA_IMAGE')) return false;

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('feature_image');

        return $media;
    }
}
