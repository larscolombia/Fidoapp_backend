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
        Schema::table('course_platform_videos', function (Blueprint $table) {
            $table->string('title')->after('id')->nullable();
            $table->string('duration')->after('title')->nullable();
            $table->string('thumbnail')->after('title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_platform_videos', function (Blueprint $table) {
            $table->dropColumn(['title','duration','thumbnail']);
        });
    }
};
