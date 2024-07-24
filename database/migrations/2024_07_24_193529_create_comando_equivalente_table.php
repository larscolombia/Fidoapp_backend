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
        Schema::create('comando_equivalente', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('comando_id');
            $table->foreign('comando_id')->references('id')->on('comandos')->onDelete('cascade')->onUpdate('cascade');

            $table->string('name');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comando_equivalente');
    }
};
