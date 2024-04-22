<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id('attendance_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->time('am_clock_in');
            $table->time('am_clock_out');
            $table->string('am_location')->nullable()->comment('WFH,OFFICE,ONFIELD');
            $table->string('am_latitude')->nullable();
            $table->string('am_longitude')->nullable();
            $table->time('pm_clock_in');
            $table->time('pm_clock_out');
            $table->string('pm_location')->nullable()->comment('WFH,OFFICE,ONFIELD');
            $table->string('pm_latitude')->nullable();
            $table->string('pm_longitude')->nullable();            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
