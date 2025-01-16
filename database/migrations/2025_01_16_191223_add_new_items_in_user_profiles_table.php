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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('tags')->after('user_id')->nullable();
            $table->string('pdf')->after('tags')->nullable();
            $table->string('professional_title')->after('pdf')->nullable();
            $table->string('validation_number')->after('professional_title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['tags','pdf','professional_title','validation_number']);
        });
    }
};
