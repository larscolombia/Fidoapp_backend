<?php

use App\Models\Coin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Coin::updateOrCreate(
            [
                'symbol' => 'ƒ'
            ],
            [
                'minimum_recharge' => 1,
                'conversion_rate' => 1
            ]
        );
    }

};
