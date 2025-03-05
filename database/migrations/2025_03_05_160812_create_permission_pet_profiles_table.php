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
        Schema::create('permission_pet_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permission_profile_id');
            $table->unsignedBigInteger('pet_id');
            $table->date('expiration');
            $table->foreign('permission_profile_id')->references('id')->on('permission_profiles')->onDelete('cascade');
            $table->foreign('pet_id')->references('id')->on('pets')->onDelete('cascade');
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
        Schema::dropIfExists('permission_pet_profiles');
    }
};
