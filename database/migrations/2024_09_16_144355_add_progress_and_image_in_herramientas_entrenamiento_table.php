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
        Schema::table('herramientas_entrenamiento', function (Blueprint $table) {
            $table->string('image')->after('type_id')->nullable();
            $table->float('progress')->after('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('herramientas_entrenamiento', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->dropColumn('progress');
        });
    }
};
