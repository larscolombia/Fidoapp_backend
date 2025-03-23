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
            "expert": {"description": "Consulta"},
            "expert_2": {"description": "Evaluación"},
            "expert_3": {"description": "Consultas"},
            "expert_4": {"description": "Actualización de data del perro"},
            "expert_5": {"description": "Actualización de vacuna"}
        }';

        // Decodificar el JSON
        $data = json_decode($jsonData, true);

        // Preparar los datos para la inserción
        $insertData = array_map(function ($item) {
            return ['description' => $item['description'], 'created_at' => now(), 'updated_at' => now()];
        }, $data);

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
