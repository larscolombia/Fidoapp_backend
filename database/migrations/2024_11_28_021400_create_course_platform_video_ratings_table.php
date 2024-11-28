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
        Schema::create('course_platform_video_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_platform_video_id');
            $table->longText('review_msg')->nullable();
            $table->double('rating')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('course_platform_video_id')->references('id')->on('course_platform_videos')->onDelete('cascade');
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
        Schema::dropIfExists('course_platform_video_ratings');
    }
};
