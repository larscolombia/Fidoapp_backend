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
        Schema::create('activity_levels', function (Blueprint $table) {
            $table->id();

            // Relación con la mascota
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
        
            // Datos de actividad física
            $table->integer('daily_steps')->unsigned()->nullable(); // Número de pasos diarios
            $table->float('distance_covered', 8, 2)->nullable(); // Distancia recorrida en kilómetros
            $table->integer('calories_burned')->unsigned()->nullable(); // Calorías quemadas
            $table->integer('active_minutes')->unsigned()->nullable(); // Minutos de actividad física
        
            // Metas de actividad física
            $table->integer('goal_steps')->unsigned()->nullable(); // Meta de pasos diarios
            $table->float('goal_distance', 8, 2)->nullable(); // Meta de distancia en kilómetros
            $table->integer('goal_calories')->unsigned()->nullable(); // Meta de calorías quemadas
            $table->integer('goal_active_minutes')->unsigned()->nullable(); // Meta de minutos de actividad física
        
            // Timestamps para control de registros
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
        Schema::dropIfExists('activity_levels');
    }
};
