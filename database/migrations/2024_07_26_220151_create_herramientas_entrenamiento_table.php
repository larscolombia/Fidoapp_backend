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
        Schema::create('herramientas_entrenamiento', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');

            $table->unsignedBigInteger('type_id');
            $table->foreign('type_id')->references('id')->on('herramientas_entrenamiento_type')->onDelete('cascade')->onUpdate('cascade');

            $table->string('audio');
            $table->enum('status', ['active', 'inactive']);
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
        Schema::dropIfExists('herramientas_entrenamiento');
    }
};
