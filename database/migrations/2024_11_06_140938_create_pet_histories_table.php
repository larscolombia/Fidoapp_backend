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
        Schema::create('pet_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('vacuna_id')->nullable();
            $table->unsignedBigInteger('antidesparasitante_id')->nullable();
            $table->unsignedBigInteger('antigarrapata_id')->nullable();
            $table->unsignedBigInteger('veterinarian_id');
            $table->text('medical_conditions')->nullable();
            $table->text('test_results')->nullable();
            $table->integer('vet_visits')->nullable();
            $table->foreign('vacuna_id')->references('id')->on('vacunas');
            $table->foreign('antidesparasitante_id')->references('id')->on('antidesparasitantes');
            $table->foreign('antigarrapata_id')->references('id')->on('antigarrapatas');
            $table->foreign('veterinarian_id')->references('id')->on('users');
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
        Schema::dropIfExists('pet_histories');
    }
};
