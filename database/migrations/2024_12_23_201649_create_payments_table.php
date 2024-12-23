<?php

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
        Schema::create('payments', function (Blueprint $table) {
            Schema::create('payments', function (Blueprint $table) {
                $table->increments('id');
                $table->double('amount', 10, 2)->default(0);
                $table->string('description', 255)->nullable();
                $table->bigInteger('user_id')->unsigned();
                $table->integer('payment_method_id')->unsigned();
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('users');
                $table->foreign('payment_method_id')->references('id')->on('settings');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
