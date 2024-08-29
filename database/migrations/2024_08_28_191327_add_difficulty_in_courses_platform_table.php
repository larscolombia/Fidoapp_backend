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
            $table->enum('difficulty',[1,2,3])->after('price')
            ->comment('1 = Beginner , 2 = Intermediate , 3 = Advanced')
            ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses_platform', function (Blueprint $table) {
            $table->dropColumn('difficulty');
        });
    }
};
