<?php

namespace Modules\Pet\database\seeders;

use Endroid\QrCode\Builder\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Pet\Models\Pet;
use Modules\Pet\Models\PetType;
use Modules\Pet\Models\Breed;
use Illuminate\Support\Arr;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PetTableSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks!
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        /*
         * Services Seed
         * ------------------
         */

        // DB::table('pettype')->truncate();
        // echo "Truncate: pettype \n";

        if (env('IS_DUMMY_DATA')) {
            $data = [
                // [
                //     'name' => 'Oliver',
                //     'slug' => 'oliver',
                //     'pettype_id' => 'cat',
                //     'breed_id' =>'abyssinian',
                //     'age' => '2 años',
                //     'weight' => 4,
                //     'weight_unit' => 'kg',
                //     'height' => 22,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 2,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/2/oliver_cat_m.png'),
                // ],
                [
                    'name' => 'Beau',
                    'slug' => 'beau',
                    'pettype_id' => 'dog',
                    'breed_id' =>'labrador-retriever',
                    'age' => '13 años',
                    'weight' => 31,
                    'weight_unit' => 'kg',
                    'height' => 60,
                    'height_unit' => 'cm',
                    'gender' => 'male',
                    'user_id' => 2,
                    'status' => 1,
                    'image' => public_path('/dummy-images/pets/2/beau_dog_m.png'),
                ],
                [
                    'name' => 'Sadie',
                    'slug' => 'sadie',
                    'pettype_id' => 'dog',
                    'breed_id' =>'boston-terrier',
                    'age' => '8 años',
                    'weight' => 5,
                    'weight_unit' => 'kg',
                    'height' => 41,
                    'height_unit' => 'cm',
                    'gender' => 'female',
                    'user_id' => 2,
                    'status' => 1,
                    'image' => public_path('/dummy-images/pets/2/sadie_dog_f.png'),
                ],
                // [
                //     'name' => 'Thumper',
                //     'slug' => 'thumper',
                //     'pettype_id' => 'rabbit',
                //     'breed_id' =>'dutch-rabbit',
                //     'age' => '3 años',
                //     'weight' => 2,
                //     'weight_unit' => 'kg',
                //     'height' => 22,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 2,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/2/thumper_rabbit_m.png'),
                // ],
                // [
                //     'name' => 'Ace',
                //     'slug' => 'ace',
                //     'pettype_id' => 'fish',
                //     'breed_id' =>'betta-fish',
                //     'age' => '1 años',
                //     'weight' => 8,
                //     'weight_unit' => 'g',
                //     'height' => 7,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 2,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/2/ace_fish_m.png'),
                // ],
                // [
                //     'name' => 'Arthur',
                //     'slug' => 'arthur',
                //     'pettype_id' => 'bird',
                //     'breed_id' =>'budgerigar',
                //     'age' => '4 años',
                //     'weight' => 30,
                //     'weight_unit' => 'g',
                //     'height' => 18,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 2,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/2/arthur_bird_m.png'),
                // ],
                // [
                //     'name' => 'Duchess',
                //     'slug' => 'duchess',
                //     'pettype_id' => 'horse',
                //     'breed_id' =>'andalusian',
                //     'age' => '10 años',
                //     'weight' => 420,
                //     'weight_unit' => 'kg',
                //     'height' => 4.98,
                //     'height_unit' => 'ft',
                //     'gender' => 'male',
                //     'user_id' => 2,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/2/duchess_horse_f.png'),
                // ],

                //3

                // [
                //     'name' => 'Luna',
                //     'slug' => 'luna',
                //     'pettype_id' => 'cat',
                //     'breed_id' =>'ragdoll',
                //     'age' => '5 años',
                //     'weight' => 7,
                //     'weight_unit' => 'kg',
                //     'height' => 25,
                //     'height_unit' => 'cm',
                //     'gender' => 'female',
                //     'user_id' => 3,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/3/1.luna_cat_f.png'),
                // ],
                [
                    'name' => 'Daisy',
                    'slug' => 'daisy',
                    'pettype_id' => 'dog',
                    'breed_id' =>'bulldog',
                    'age' => '9 años',
                    'weight' => 8.9,
                    'weight_unit' => 'kg',
                    'height' => 21,
                    'height_unit' => 'cm',
                    'gender' => 'female',
                    'user_id' => 3,
                    'status' => 1,
                    'image' => public_path('/dummy-images/pets/3/2.daisy_dog_f.png'),
                ],
                // [
                //     'name' => 'Peter',
                //     'slug' => 'peter',
                //     'pettype_id' => 'rabbit',
                //     'breed_id' =>'netherland-dwarf',
                //     'age' => '2 años',
                //     'weight' => 700,
                //     'weight_unit' => 'g',
                //     'height' => 14,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 3,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/3/3.peter_rabbit_m.png'),
                // ],
                // [
                //     'name' => 'Ala',
                //     'slug' => 'ala',
                //     'pettype_id' => 'bird',
                //     'breed_id' =>'blue-&-gold-macaw',
                //     'age' => '11 month',
                //     'weight' => 200,
                //     'weight_unit' => 'g',
                //     'height' => 20,
                //     'height_unit' => 'cm',
                //     'gender' => 'female',
                //     'user_id' => 3,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/3/4.ala_bird_f.png'),
                // ],
                // [
                //     'name' => 'Bianca',
                //     'slug' => 'bianca',
                //     'pettype_id' => 'mouse',
                //     'breed_id' =>'fawn',
                //     'age' => '4 años',
                //     'weight' => 30,
                //     'weight_unit' => 'g',
                //     'height' => 18,
                //     'height_unit' => 'cm',
                //     'gender' => 'female',
                //     'user_id' => 3,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/3/5.bianca_mouse_f.png'),
                // ],

                // //4


                // [
                //     'name' => 'Bella',
                //     'slug' => 'Bella',
                //     'pettype_id' => 'cat',
                //     'breed_id' =>'sphynx',
                //     'age' => '3 años',
                //     'weight' => 5.4,
                //     'weight_unit' => 'kg',
                //     'height' => 24,
                //     'height_unit' => 'cm',
                //     'gender' => 'female',
                //     'user_id' => 4,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/4/1.bella_cat_f.png'),
                // ],
                [
                    'name' => 'Tucker',
                    'slug' => 'Tucker',
                    'pettype_id' => 'dog',
                    'breed_id' =>'german-shepherd',
                    'age' => '7 años',
                    'weight' => 30,
                    'weight_unit' => 'kg',
                    'height' => 65,
                    'height_unit' => 'cm',
                    'gender' => 'male',
                    'user_id' => 4,
                    'status' => 1,
                    'image' => public_path('/dummy-images/pets/4/2.tucker_dog_m.png'),
                ],
                // [
                //     'name' => 'Remy',
                //     'slug' => 'Remy',
                //     'pettype_id' => 'mouse',
                //     'breed_id' =>'berkshire',
                //     'age' => '2 años',
                //     'weight' => 20,
                //     'weight_unit' => 'g',
                //     'height' => 10,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 4,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/4/3.remy_mouse_m.png'),
                // ],
                // [
                //     'name' => 'Dwight',
                //     'slug' => 'Dwight',
                //     'pettype_id' => 'bird',
                //     'breed_id' =>'lovebirds',
                //     'age' => '4 años',
                //     'weight' => 50,
                //     'weight_unit' => 'g',
                //     'height' => 14,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 4,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/4/4.dwight_bird_m.png'),
                // ],
                // [
                //     'name' => 'Clara',
                //     'slug' => 'Clara',
                //     'pettype_id' => 'horse',
                //     'breed_id' =>'morgan',
                //     'age' => '6 años',
                //     'weight' => 430,
                //     'weight_unit' => 'kg',
                //     'height' => 4.92,
                //     'height_unit' => 'ft',
                //     'gender' => 'female',
                //     'user_id' => 4,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/4/5.clara_horse_f.png'),
                // ],

                //5

                // [
                //     'name' => 'Angel',
                //     'slug' => 'angel',
                //     'pettype_id' => 'cat',
                //     'breed_id' =>'sphynx',
                //     'age' => '3 años',
                //     'weight' => 5.4,
                //     'weight_unit' => 'kg',
                //     'height' => 24,
                //     'height_unit' => 'cm',
                //     'gender' => 'female',
                //     'user_id' => 5,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/5/1.angel_cat_f.png'),
                // ],
                [
                    'name' => 'Cooper',
                    'slug' => 'cooper',
                    'pettype_id' => 'dog',
                    'breed_id' =>'golden-retriever',
                    'age' => '5 años',
                    'weight' => 31,
                    'weight_unit' => 'kg',
                    'height' => 60,
                    'height_unit' => 'cm',
                    'gender' => 'male',
                    'user_id' => 5,
                    'status' => 1,
                    'image' => public_path('/dummy-images/pets/5/2.cooper_dog_m.png'),
                ],
                // [
                //     'name' => 'Jaws',
                //     'slug' => 'jaws',
                //     'pettype_id' => 'squirrel',
                //     'breed_id' =>'western-gray',
                //     'age' => '1 años',
                //     'weight' => 500,
                //     'weight_unit' => 'g',
                //     'height' => 0.533,
                //     'height_unit' => 'm',
                //     'gender' => 'female',
                //     'user_id' => 5,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/5/3.jaws_squirrel_f.png'),
                // ],
                // [
                //     'name' => 'Achilles',
                //     'slug' => 'achilles',
                //     'pettype_id' => 'turtle',
                //     'breed_id' =>'red-eared',
                //     'age' => '4 años',
                //     'weight' => 3,
                //     'weight_unit' => 'kg',
                //     'height' => 20,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 5,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/5/4.achilles_turtle_m.png'),
                // ],

                // //6

                // [
                //     'name' => 'Jack',
                //     'slug' => 'Jack',
                //     'pettype_id' => 'cat',
                //     'breed_id' =>'american-shorthair',
                //     'age' => '6 años',
                //     'weight' => 7.1,
                //     'weight_unit' => 'kg',
                //     'height' => 25,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 6,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/6/1.jack_cat_m.png'),
                // ],
                [
                    'name' => 'Bear',
                    'slug' => 'Bear',
                    'pettype_id' => 'dog',
                    'breed_id' =>'bulldog',
                    'age' => '4 años',
                    'weight' => 19.5,
                    'weight_unit' => 'kg',
                    'height' => 35,
                    'height_unit' => 'cm',
                    'gender' => 'male',
                    'user_id' => 6,
                    'status' => 1,
                    'image' => public_path('/dummy-images/pets/6/2.milo_dog_m.png'),
                ],
                // [
                //     'name' => 'Beth',
                //     'slug' => 'Beth',
                //     'pettype_id' => 'chameleon',
                //     'breed_id' =>"oustalet's",
                //     'age' => '2 años',
                //     'weight' => 200,
                //     'weight_unit' => 'g',
                //     'height' => 65,
                //     'height_unit' => 'cm',
                //     'gender' => 'female',
                //     'user_id' => 6,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/6/3.beth_chameleon_f.png'),
                // ],
                // [
                //     'name' => 'Colt',
                //     'slug' => 'Colt',
                //     'pettype_id' => 'horse',
                //     'breed_id' =>'arabian',
                //     'age' => '7 años',
                //     'weight' => 400,
                //     'weight_unit' => 'kg',
                //     'height' => 4.59,
                //     'height_unit' => 'ft',
                //     'gender' => 'male',
                //     'user_id' => 6,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/6/4.colt_horse_m.png'),
                // ],

                // //7
                // [
                //     'name' => 'Leo',
                //     'slug' => 'Leo',
                //     'pettype_id' => 'cat',
                //     'breed_id' =>'bengal',
                //     'age' => '5 años',
                //     'weight' => 6.2,
                //     'weight_unit' => 'kg',
                //     'height' => 30,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 7,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/7/1.leo_cat_m.png'),
                // ],
                [
                    'name' => 'Bailey',
                    'slug' => 'Bailey',
                    'pettype_id' => 'dog',
                    'breed_id' =>'golden-retriever',
                    'age' => '3 años',
                    'weight' => 27.5,
                    'weight_unit' => 'kg',
                    'height' => 55,
                    'height_unit' => 'cm',
                    'gender' => 'female',
                    'user_id' => 7,
                    'status' => 1,
                    'image' => public_path('/dummy-images/pets/7/2.bailey_dog_f.png'),
                ],
                // [
                //     'name' => 'Birdie',
                //     'slug' => 'Birdie',
                //     'pettype_id' => 'bird',
                //     'breed_id' =>'pionus',
                //     'age' => '3 años',
                //     'weight' => 300,
                //     'weight_unit' => 'g',
                //     'height' => 28,
                //     'height_unit' => 'cm',
                //     'gender' => 'female',
                //     'user_id' => 7,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/7/3.birdie_bird_f.png'),
                // ],
                // [
                //     'name' => 'Cookie',
                //     'slug' => 'cookie',
                //     'pettype_id' => 'horse',
                //     'breed_id' =>'friesian',
                //     'age' => '6 años',
                //     'weight' => 500,
                //     'weight_unit' => 'kg',
                //     'height' => 5.57,
                //     'height_unit' => 'ft',
                //     'gender' => 'female',
                //     'user_id' => 7,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/7/4.cookie_horse_f.png'),
                // ],
                /////////////////////////////////////////////////////
                //8
                // [
                //     'name' => 'Coco',
                //     'slug' => 'Coco',
                //     'pettype_id' => 'cat',
                //     'breed_id' =>'siamese',
                //     'age' => '3 años',
                //     'weight' => 4,
                //     'weight_unit' => 'kg',
                //     'height' => 28,
                //     'height_unit' => 'cm',
                //     'gender' => 'female',
                //     'user_id' => 8,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/8/1.coco_cat_f.png'),
                // ],
                [
                    'name' => 'Sophie',
                    'slug' => 'Sophie',
                    'pettype_id' => 'dog',
                    'breed_id' =>'rottweiler',
                    'age' => '6 años',
                    'weight' => 65,
                    'weight_unit' => 'kg',
                    'height' => 2.82,
                    'height_unit' => 'ft',
                    'gender' => 'female',
                    'user_id' => 8,
                    'status' => 1,
                    'image' => public_path('/dummy-images/pets/8/2.sophie_dog_f.png'),
                ],
                // [
                //     'name' => 'Rambo',
                //     'slug' => 'Rambo',
                //     'pettype_id' => 'squirrel',
                //     'breed_id' =>'eastern-gray',
                //     'age' => '2 años',
                //     'weight' => 0.7,
                //     'weight_unit' => 'kg',
                //     'height' => 25,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 8,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/8/3.squirrel_eastern_gray_m.png'),
                // ],
                // [
                //     'name' => 'Brooke',
                //     'slug' => 'Brooke',
                //     'pettype_id' => 'chameleon',
                //     'breed_id' =>'graceful',
                //     'age' => '1 años',
                //     'weight' => 100,
                //     'weight_unit' => 'g',
                //     'height' => 60,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 8,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/8/4.brooke_chameleon_m.png'),
                // ],

                //9

                // [
                //     'name' => 'Milo',
                //     'slug' => 'Milo',
                //     'pettype_id' => 'cat',
                //     'breed_id' =>'british-shorthair',
                //     'age' => '7 años',
                //     'weight' => 5,
                //     'weight_unit' => 'kg',
                //     'height' => 35,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 9,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/9/1.milo_cat_m.png'),
                // ],
                [
                    'name' => 'Buddy',
                    'slug' => 'Buddy',
                    'pettype_id' => 'dog',
                    'breed_id' =>'beagle',
                    'age' => '8 años',
                    'weight' => 11,
                    'weight_unit' => 'kg',
                    'height' => 38,
                    'height_unit' => 'cm',
                    'gender' => 'male',
                    'user_id' => 9,
                    'status' => 1,
                    'image' => public_path('/dummy-images/pets/9/2.buddy_dog_m.png'),
                ],
                // [
                //     'name' => 'Maximus',
                //     'slug' => 'Maximus',
                //     'pettype_id' => 'squirrel',
                //     'breed_id' =>'red',
                //     'age' => '3 años',
                //     'weight' => 450,
                //     'weight_unit' => 'g',
                //     'height' => 22,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 9,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/9/3.maximus_squirrel_m.png'),
                // ],
                // [
                //     'name' => 'Cher',
                //     'slug' => 'Cher',
                //     'pettype_id' => 'bird',
                //     'breed_id' =>'monk-parakeet',
                //     'age' => '10 meses',
                //     'weight' => 120,
                //     'weight_unit' => 'g',
                //     'height' => 28,
                //     'height_unit' => 'cm',
                //     'gender' => 'female',
                //     'user_id' => 9,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/9/4.cher_bird_f.png'),
                // ],
                // [
                //     'name' => 'Misty',
                //     'slug' => 'misty',
                //     'pettype_id' => 'horse',
                //     'breed_id' =>'palomino',
                //     'age' => '12 años',
                //     'weight' => 430,
                //     'weight_unit' => 'kg',
                //     'height' => 4.73,
                //     'height_unit' => 'ft',
                //     'gender' => 'female',
                //     'user_id' => 9,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/9/5.misty_horse_f.png'),
                // ],

                //10

                // [
                //     'name' => 'Charlie',
                //     'slug' => 'Charlie',
                //     'pettype_id' => 'cat',
                //     'breed_id' =>'himalayan',
                //     'age' => '2 años',
                //     'weight' => 8.1,
                //     'weight_unit' => 'kg',
                //     'height' => 0.406,
                //     'height_unit' => 'm',
                //     'gender' => 'male',
                //     'user_id' => 10,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/10/1.charlie_cat_m.png'),
                // ],
                [
                    'name' => 'Rocky',
                    'slug' => 'Rocky',
                    'pettype_id' => 'dog',
                    'breed_id' =>'poodle',
                    'age' => '4 años',
                    'weight' => 6.2,
                    'weight_unit' => 'kg',
                    'height' => 37,
                    'height_unit' => 'cm',
                    'gender' => 'male',
                    'user_id' => 10,
                    'status' => 1,
                    'image' => public_path('/dummy-images/pets/10/2.rocky_dog_m.png'),
                ],
                // [
                //     'name' => 'Ariel',
                //     'slug' => 'Ariel',
                //     'pettype_id' => 'fish',
                //     'breed_id' =>'neon-tetra',
                //     'age' => '60 days',
                //     'weight' => 8000,
                //     'weight_unit' => 'mg',
                //     'height' => 5,
                //     'height_unit' => 'cm',
                //     'gender' => 'female',
                //     'user_id' => 10,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/10/3.ariel_fish_f.png'),
                // ],
                // [
                //     'name' => 'Kentucky',
                //     'slug' => 'kentucky',
                //     'pettype_id' => 'horse',
                //     'breed_id' =>'thoroughbred',
                //     'age' => '12 años',
                //     'weight' => 521,
                //     'weight_unit' => 'kg',
                //     'height' => 5,
                //     'height_unit' => 'ft',
                //     'gender' => 'male',
                //     'user_id' => 10,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/10/4.kentucky_horse_m.png'),
                // ],

                //11

                // [
                //     'name' => 'Shinu',
                //     'slug' => 'Shinu',
                //     'pettype_id' => 'cat',
                //     'breed_id' =>'ragdoll',
                //     'age' => '3 años',
                //     'weight' => 4.2,
                //     'weight_unit' => 'kg',
                //     'height' => 24,
                //     'height_unit' => 'cm',
                //     'gender' => 'female',
                //     'user_id' => 11,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/11/1.shinu_cat_f.png'),
                // ],
                // [
                //     'name' => 'Max',
                //     'slug' => 'Max',
                //     'pettype_id' => 'cat',
                //     'breed_id' =>'persian',
                //     'age' => '8 años',
                //     'weight' => 5.2,
                //     'weight_unit' => 'kg',
                //     'height' => 23,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 11,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/11/2.max_cat_m.png'),
                // ],
                [
                    'name' => 'Roxy',
                    'slug' => 'Roxy',
                    'pettype_id' => 'dog',
                    'breed_id' =>'rottweiler',
                    'age' => '7 años',
                    'weight' => 9.8,
                    'weight_unit' => 'kg',
                    'height' => 30,
                    'height_unit' => 'cm',
                    'gender' => 'female',
                    'user_id' => 11,
                    'status' => 1,
                    'image' => public_path('/dummy-images/pets/11/3.roxy_dog_f.png'),
                ],
                // [
                //     'name' => 'Dale',
                //     'slug' => 'Dale',
                //     'pettype_id' => 'bird',
                //     'breed_id' =>'parrots',
                //     'age' => '6 años',
                //     'weight' => 900,
                //     'weight_unit' => 'gm',
                //     'height' => 22,
                //     'height_unit' => 'cm',
                //     'gender' => 'male',
                //     'user_id' => 11,
                //     'status' => 1,
                //     'image' => public_path('/dummy-images/pets/11/4.dale_bird_m.png'),
                // ],


            ];
            foreach ($data as $key => $value) {
                $pettype = PetType::where('slug',$value['pettype_id'])->first();
                $breed = Breed::where('slug',$value['breed_id'])->first();
                $image = $value['image'] ?? null;
                $pet = Arr::except($value, ['image']);
                $pet = [
                    'name' => $value['name'],
                    'slug' => $value['slug'],
                    'pettype_id' =>  $pettype->id,
                    'breed_id' =>  $breed->id,
                    'age' => $value['age'],
                    'weight' => $value['weight'],
                    'weight_unit' => $value['weight_unit'],
                    'height' => $value['height'],
                    'height_unit' => $value['height_unit'],
                    'gender' => $value['gender'],
                    'user_id' =>$value['user_id'],
                    'status' => $value['status'],
                    'qr_code' => $this->safeGenerateQrCode($pet),
                ];
                $pet = Pet::create($pet);
                if (isset($image)) {
                    $this->attachFeatureImage($pet, $image);

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

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('pet_image');

        return $media;
    }

    private function safeGenerateQrCode($pet)
    {
        try {
            return $this->generateQrCode($pet);
        } catch (\Exception $e) {
            // Manejo de errores: registra el error y retorna null
            \Log::error('Error al generar el código QR: ' . $e->getMessage());
            return null;
        }
    }

    public function generateQrCode($pet)
    {
        // Convierte el array $pet a una cadena JSON
        //$data = json_encode($pet);
        $slug = $pet['slug'];
         // Construye la URL para la API de qrserver.com usando el slug
         $url = route('pet_detail.profile_public', ['slug' => $slug]);
        // Construye la URL para la API de qrserver.com
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/';
        $size = '150x150'; // Tamaño del código QR
        $url = $qrCodeUrl . '?size=' . $size . '&data=' . urlencode($url);

        // Realiza la solicitud para obtener el código QR
        $response = Http::get($url);

        // Verifica si la solicitud fue exitosa
        if ($response->successful()) {
            // Obtén el contenido de la imagen del QR Code
            $qrCodeContent = $response->body();

            // Genera un nombre de archivo basado en el timestamp actual
            $timestamp = time(); // Obtiene el timestamp actual
            $filename = 'qr_code_' . $timestamp . '.png'; // Nombre del archivo
            $path = 'images/qr_codes/' . $filename;

            // Guarda el archivo en el disco público
            $saved = File::put('public/' . $path, $qrCodeContent);

            // Verifica si el archivo se guardó correctamente
            if ($saved) {
                return $path;
            } else {
                throw new \Exception("No se pudo guardar el archivo QR code.");
            }
        } else {
            throw new \Exception("Error al generar el código QR: " . $response->status());
        }
    }

}
