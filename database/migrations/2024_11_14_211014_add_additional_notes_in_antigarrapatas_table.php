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
        Schema::table('antigarrapatas', function (Blueprint $table) {
            $table->string('additional_notes')->after('fecha_refuerzo_antigarrapata')->nullable();
            $table->string('weight')->after('additional_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('antigarrapatas', function (Blueprint $table) {
            $table->dropColumn('additional_notes');
            $table->dropColumn('weight');
        });
    }
};
