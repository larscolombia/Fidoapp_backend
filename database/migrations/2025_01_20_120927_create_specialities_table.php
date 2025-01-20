<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specialities', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->timestamps();
        });

        $this->insertData();
    }

    private function insertData()
    {
        $jsonData = '{
            "expert": {"description": "Comportamiento."},
            "expert_2": {"description": "Cardiología."},
            "expert_3": {"description": "Dermatología."},
            "expert_4": {"description": "Emergencias y Cuidados Críticos."},
            "expert_5": {"description": "Cirugía Ortopédica."},
            "expert_6": {"description": "Nutrición."},
            "expert_7": {"description": "Anestesiología."},
            "expert_8": {"description": "Entrenador de gatos experimentado, enseñando trucos divertidos y comportamientos interactivos."},
            "expert_9": {"description": "Dominando el entrenamiento con correa y el control sin correa para asegurar paseos seguros y agradables."},
            "expert_10": {"description": "Experto en entrenamiento con clicker, utilizando refuerzo positivo para un modelado conductual preciso."},
            "expert_11": {"description": "Entrenador de perros de servicio certificado, proporcionando entrenamiento personalizado para tareas de asistencia y apoyo."},
            "expert_12": {"description": "Especialista en modificación de comportamiento, abordando la ansiedad y la agresión con técnicas de comportamiento positivo."},
            "expert_13": {"description": "Especializado en entrenamiento de agilidad y certificación de Perro Buen Ciudadano (CGC), haciendo que el entrenamiento sea divertido y atractivo."}
          }';

          // Decodificar el JSON
          $data = json_decode($jsonData, true);

          // Preparar los datos para la inserción
          $insertData = [];
          foreach ($data as $expert) {
              $insertData[] = ['description' => $expert['description'], 'created_at' => now(), 'updated_at' => now()];
          }

          // Insertar los datos en la tabla 'specialities'
          DB::table('specialities')->insert($insertData);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('specialities');
    }
};
