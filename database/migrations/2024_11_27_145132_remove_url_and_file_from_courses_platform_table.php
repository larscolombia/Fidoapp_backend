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
        Schema::table('courses_platform', function (Blueprint $table) {
            $table->dropColumn(['url', 'file']);
        });
    }

    public function down()
    {
        Schema::table('courses_platform', function (Blueprint $table) {
            $table->string('url')->nullable();
            $table->string('file')->nullable();
        });
    }
};
