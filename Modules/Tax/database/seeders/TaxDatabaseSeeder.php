<?php

namespace Modules\Tax\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Tax\Models\Tax;

class TaxDatabaseSeeder extends Seeder
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

        $taxes = [
            [
                'title' => 'Impuesto sobre las ventas',
                'type' => 'percentage',
                'value' => 1,
            ],

            [
                'title' => 'Otros impuestos',
                'type' => 'percentage',
                'value' => 2,
            ],

            [
                'title' => 'Impuesto de servicio o impuesto sobre ingresos brutos',
                'type' => 'fixed',
                'value' => 5,
            ],
        ];
        if (env('IS_DUMMY_DATA')) {
            foreach ($taxes  as $key => $taxes_data) {
                $tax = Tax::create($taxes_data);
            }
        }

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
