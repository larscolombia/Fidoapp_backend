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
        if (!Schema::hasColumn('events', 'event_time')) {
            Schema::table('events', function (Blueprint $table) {
                $table->time('event_time')->after('end_date')->nullable();
            });
        }

    }

};
