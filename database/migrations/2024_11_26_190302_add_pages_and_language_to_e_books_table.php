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
        Schema::table('e_book', function (Blueprint $table) {
            $table->integer('number_of_pages')->after('description')->nullable();
            $table->string('language')->after('number_of_pages')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('e_book', function (Blueprint $table) {
            $table->dropColumn(['number_of_pages', 'language']);
        });
    }
};
