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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->longText('note')->nullable();
            $table->string('status')->default(config('booking.DEFAULT_STATUS'));
            $table->dateTime('start_date_time');

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_id');

            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('system_service_id');
            $table->unsignedBigInteger('pet_id');
            $table->longText('booking_extra_info')->nullable();
            $table->string('booking_type')->nullable();
            $table->double('total_amount')->default(0);
            $table->double('service_amount')->default(0);

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
        Schema::dropIfExists('bookings');
    }
};
