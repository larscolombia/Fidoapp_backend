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
        Schema::table('event_details', function (Blueprint $table) {
            // Eliminar claves foráneas existentes
            $table->dropForeign(['pet_id']);
            $table->dropForeign(['owner_id']);

            // Volver a crear claves foráneas con onDelete('cascade')
            $table->foreign('pet_id')
                  ->references('id')
                  ->on('pets')
                  ->onDelete('cascade');

            $table->foreign('owner_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_details', function (Blueprint $table) {
            // Revertir: eliminar claves foráneas con onDelete('cascade')
            $table->dropForeign(['pet_id']);
            $table->dropForeign(['owner_id']);

            // Volver a crear claves foráneas sin onDelete
            $table->foreign('pet_id')
                  ->references('id')
                  ->on('pets');

            $table->foreign('owner_id')
                  ->references('id')
                  ->on('users');
        });
    }
};
