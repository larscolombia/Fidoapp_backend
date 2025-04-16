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
        Schema::table('course_platform_user_subscriptions', function (Blueprint $table) {
            // Primero eliminar la clave foránea existente
            $table->dropForeign(['user_id']);

            // Luego crear la clave foránea con onDelete('cascade')
            $table->foreign('user_id')
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
        Schema::table('course_platform_user_subscriptions', function (Blueprint $table) {
            // Revertir: eliminar la clave foránea con onDelete('cascade')
            $table->dropForeign(['user_id']);

            // Volver a crear la clave foránea sin onDelete
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users');
        });
    }
};
