<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $platforms = [
            [
                'name'        => 'Youtube',
            ],
            [
                'name'        => 'Vimeo'
            ],
        ];

        foreach($platforms as $platform){
            Platform::create($platform);
        }
    }
}
