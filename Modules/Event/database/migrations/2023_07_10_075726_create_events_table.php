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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->time('event_time')->nullable();
            $table->string('image')->nullable();

            $table->string('slug')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->longText('description')->nullable();
            $table->longText('location')->nullable();
            $table->enum('tipo', ['medico', 'entrenamiento','evento']);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->boolean('status')->default(1);

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
