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
        Schema::table('pet_histories', function (Blueprint $table) {
            $table->string('name')->after('id')->nullable();
            $table->date('application_date')->after('name')->nullable();
            $table->enum('category',['1','2','3'])->after('pet_id')->nullable();
            $table->string('file')->after('vet_visits')->nullable();
            $table->string('image')->after('file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pet_histories', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('application_date');
            $table->dropColumn('category');
            $table->dropColumn('file');
            $table->dropColumn('image');
        });
    }
};
