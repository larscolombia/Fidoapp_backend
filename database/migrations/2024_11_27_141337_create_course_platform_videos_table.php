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
        Schema::create('course_platform_videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_platform_id');
            $table->string('url');
            $table->string('video');
            $table->foreign('course_platform_id')->references('id')->on('courses_platform')->onDelete('cascade');
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
        Schema::dropIfExists('course_platform_videos');
    }
};
