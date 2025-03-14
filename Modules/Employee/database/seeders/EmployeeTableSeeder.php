<?php

namespace Modules\Employee\database\seeders;

use App\Models\Branch;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Commission\Models\EmployeeCommission;
use Modules\Employee\Models\BranchEmployee;
use Modules\Employee\Models\EmployeeRating;
use Modules\Service\Models\ServiceEmployee;
use Modules\Commission\Models\Commission;
use Illuminate\Support\Arr;

class EmployeeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        /*
         * Employees Seed
         * ------------------
         */

        // DB::table('employees')->truncate();
        // echo "Truncate: employees \n";

        $employee = [

            //Vet

            [
                'first_name' => 'Dr. Felix',
                'last_name' => 'Harris',
                'email' => 'felix@gmail.com',
                'feature_image' => public_path('/dummy-images/profile/veterinarian/Dr.Felix.png'),
                'password' => Hash::make('12345678'),
                'mobile' => '1-7485961589',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'user_type' => 'vet',
                'show_in_calender' => 1,
                'about_self' => 'Experimentado en cirugías de rutina y procedimientos dentales',
                'expert' => 'Actualización de vacuna',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'latitude' => '50.909698',
                'longitude' => '-1.404351'
            ],
            [
                'first_name' => 'Dr. Jorge',
                'last_name' => 'Perez',
                'email' => 'jorge@gmail.com',
                'feature_image' => public_path('/dummy-images/profile/veterinarian/Dr.Jorge.png'),
                'password' => Hash::make('12345678'),
                'mobile' => '1-2563987415',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'user_type' => 'vet',
                'show_in_calender' => 1,
                'about_self' => 'Dedicado a proporcionar atención personalizada a cada paciente',
                'expert' => 'Actualización de data del perro',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Dr. Daniel',
                'last_name' => 'Wiliams',
                'email' => 'daniel@gmail.com',
                'feature_image' => public_path('/dummy-images/profile/veterinarian/Dr.Daniel.png'),
                'password' => Hash::make('12345678'),
                'mobile' => '1-3565478941',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'user_type' => 'vet',
                'show_in_calender' => 1,
                'about_self' => 'Experimentado en el tratamiento de una variedad de enfermedades de mascotas exóticas',
                'expert' => 'Consultas',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'latitude' => '53.3833318',
                'longitude' => '-1.404351'
            ],
            [
                'first_name' => 'Dr. Jose',
                'last_name' => 'Parry',
                'email' => 'jose@gmail.com',
                'feature_image' => public_path('/dummy-images/profile/veterinarian/Dr.Jose.png'),
                'password' => Hash::make('12345678'),
                'mobile' => '1-8574965125',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'user_type' => 'vet',
                'show_in_calender' => 1,
                'about_self' => 'Atención compasiva para mascotas con afecciones crónicas',
                'expert' => 'Consultas',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'latitude' => '51.063202',
                'longitude' => '-1.308000'
            ],
            [
                'first_name' => 'Dr. Erik',
                'last_name' => 'Simon',
                'email' => 'erik@gmail.com',
                'feature_image' => public_path('/dummy-images/profile/veterinarian/Dr.Erik.png'),
                'password' => Hash::make('12345678'),
                'mobile' => '1-5674587152',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'user_type' => 'vet',
                'show_in_calender' => 1,
                'about_self' => 'Cuidando mascotas con diversas enfermedades y lesiones oculares',
                'expert' => 'Consultas',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'latitude' => '50.063202',
                'longitude' => '-1.308000'
            ],
            [
                'first_name' => 'Dr. Parsa',
                'last_name' => 'Evana',
                'email' => 'parsa@gmail.com',
                'feature_image' => public_path('/dummy-images/profile/veterinarian/Dr.Parsa.png'),
                'password' => Hash::make('12345678'),
                'mobile' => '1-4578965541',
                'gender' => 'female',
                'email_verified_at' => Carbon::now(),
                'user_type' => 'vet',
                'show_in_calender' => 1,
                'about_self' => 'Experiencia en acupuntura para el manejo del dolor y la reducción del estrés',
                'expert' => 'Consultas',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Dr. Erica',
                'last_name' => 'Mendiz',
                'email' => 'erica@gmail.com',
                'feature_image' => public_path('/dummy-images/profile/veterinarian/Dr.Erica.png'),
                'password' => Hash::make('12345678'),
                'mobile' => '1-4578965541',
                'gender' => 'female',
                'email_verified_at' => Carbon::now(),
                'user_type' => 'vet',
                'show_in_calender' => 1,
                'about_self' => 'Dedicado a proporcionar atención inmediata y compasiva en emergencias',
                'expert' => 'Actualización de data del perro',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],



            // trainer

            [
                'first_name' => 'Tristan',
                'last_name' => 'Erickson',
                'email' => 'tristan@gmail.com',
                'feature_image' => public_path('/dummy-images/profile/trainer/Tristan.png'),
                'password' => Hash::make('12345678'),
                'mobile' => '1-4752125545',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'user_type' => 'trainer',
                'show_in_calender' => 1,
                'about_self' => 'Entrenador experimentado, capacitado en obediencia avanzada y control sin correa.',
                'expert' => 'Consulta',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Vernon',
                'last_name' => 'Simon',
                'email' => 'vernon@gmail.com',
                'feature_image' => public_path('/dummy-images/profile/trainer/Vernon.png'),
                'password' => Hash::make('12345678'),
                'mobile' => '1-4515478569',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'user_type' => 'trainer',
                'show_in_calender' => 1,
                'about_self' => 'Entrenador paciente y positivo, especializado en la socialización de cachorros y obediencia básica.',
                'expert' => 'Consulta',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Oscar',
                'last_name' => 'Miles',
                'email' => 'oscar@gmail.com',
                'feature_image' => public_path('/dummy-images/profile/trainer/Oscar.png'),
                'password' => Hash::make('12345678'),
                'mobile' => '1-5541547857',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'user_type' => 'trainer',
                'show_in_calender' => 1,
                'about_self' => 'Entrenador compasivo enfocado en el manejo del miedo y la ansiedad para mascotas estresadas.',
                'expert' => 'Evaluación',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Tracy',
                'last_name' => 'Jones',
                'email' => 'tracy@gmail.com',
                'feature_image' => public_path('/dummy-images/profile/trainer/Tracy.png'),
                'password' => Hash::make('12345678'),
                'mobile' => '1-4759025523',
                'gender' => 'female',
                'email_verified_at' => Carbon::now(),
                'user_type' => 'trainer',
                'show_in_calender' => 1,
                'about_self' => 'Especializado en el entrenamiento de perros de servicio para empoderar a personas con discapacidades y sus compañeros caninos.',
                'expert' => 'Consulta',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Parks',
                'email' => 'emily@gmail.com',
                'feature_image' => public_path('/dummy-images/profile/trainer/Emily.png'),
                'password' => Hash::make('12345678'),
                'mobile' => '1-3515478545',
                'gender' => 'female',
                'email_verified_at' => Carbon::now(),
                'user_type' => 'trainer',
                'show_in_calender' => 1,
                'about_self' => 'Dedicado a ayudar a las mascotas a superar desafíos de comportamiento mediante el refuerzo positivo.',
                'expert' => 'Evaluación',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Molly',
                'last_name' => 'Jorden',
                'email' => 'molly@gmail.com',
                'feature_image' => public_path('/dummy-images/profile/trainer/Molly.png'),
                'password' => Hash::make('12345678'),
                'mobile' => '1-4585478546',
                'gender' => 'female',
                'email_verified_at' => Carbon::now(),
                'user_type' => 'trainer',
                'show_in_calender' => 1,
                'about_self' => 'Experimentado en el entrenamiento específico de razas y actividades creativas de enriquecimiento.',
                'expert' => 'Evaluación',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],


        ];

        if (env('IS_DUMMY_DATA')) {
            $commission = Commission::first();
            foreach ($employee  as $key => $employee_data) {

                $image = $employee_data['feature_image'] ?? null;
                $empData = Arr::except($employee_data, ['feature_image', 'about_self', 'expert']);
                $emp = User::create($empData);
                $emp->assignRole($emp->user_type);
                if (isset($image)) {
                    $this->attachFeatureImage($emp, $image);
                }

                $branchId = get_pet_center_id();

                BranchEmployee::create([
                    'branch_id' => $branchId,
                    'employee_id' => $emp->id,
                ]);

                EmployeeCommission::create([
                    'employee_id' => $emp->id,
                    'commission_id' => $commission->id,
                ]);

                UserProfile::create([
                    'user_id' => $emp->id,
                    'about_self' => $employee_data['about_self'],
                    'expert' => $employee_data['expert']
                ]);

                // $this->dummyReview($emp->id);

            }
        }


        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
    private function attachFeatureImage($model, $publicPath)
    {
        if (!env('IS_DUMMY_DATA_IMAGE')) return false;

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('profile_image');

        return $media;
    }
    private function dummyReview($emp_id)
    {
        $employeerating = [
            [
                'employee_id' => $emp_id,
                'user_id' => fake()->numberBetween(2, 40),
                'review_msg' => 'Awesome service',
                'rating' => fake()->numberBetween(3, 5),
            ],
            [
                'employee_id' => $emp_id,
                'user_id' => fake()->numberBetween(2, 40),
                'review_msg' => 'Very nice',
                'rating' => fake()->numberBetween(3, 5),
            ],
            [
                'employee_id' => $emp_id,
                'user_id' => fake()->numberBetween(2, 40),
                'review_msg' => 'Very Good',
                'rating' => fake()->numberBetween(3, 5),
            ],
            [
                'employee_id' => $emp_id,
                'user_id' => fake()->numberBetween(2, 40),
                'review_msg' => 'Nice',
                'rating' => fake()->numberBetween(3, 5),
            ],
            [
                'employee_id' => $emp_id,
                'user_id' => fake()->numberBetween(2, 40),
                'review_msg' => 'Awesome service',
                'rating' => fake()->numberBetween(3, 5),
            ],
            [
                'employee_id' => $emp_id,
                'user_id' => fake()->numberBetween(2, 40),
                'review_msg' => 'Good service',
                'rating' => fake()->numberBetween(3, 5),
            ],
        ];
        foreach ($employeerating  as $key => $employeeRating_data) {
            EmployeeRating::create($employeeRating_data);
        }
    }
}
