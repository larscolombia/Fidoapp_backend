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
        Schema::create('antidesparasitantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained();
            $table->string('antidesparasitante_name');
            $table->date('fecha_aplicacion');
            $table->date('fecha_refuerzo_antidesparasitante');
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
        Schema::dropIfExists('antidesparasitantes');
    }
};
