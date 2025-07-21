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
        Schema::create('time_sheets', function (Blueprint $table) {
            $table->id('time_sheet_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('date')->nullable();
            $table->string('shift_schedule')->nullable();
            $table->time('time_in')->default('00:00');
            $table->string('in_location')->nullable()->comment('WFH,OFFICE,ONFIELD');
            $table->string('in_latitude')->nullable();
            $table->string('in_longitude')->nullable();
            $table->time('break_time_out')->default('00:00');
            $table->time('break_time_in')->default('00:00');
            $table->time('time_out')->default('00:00');
            $table->string('out_location')->nullable()->comment('WFH,OFFICE,ONFIELD');
            $table->string('out_latitude')->nullable();
            $table->string('out_longitude')->nullable();   
            $table->date('out_date')->nullable();         
            $table->time('time_in_2')->default('00:00');
            $table->time('time_out_2')->default('00:00');
            $table->time('late_time')->default('00:00');
            $table->time('over_time')->default('00:00');
            $table->string('remarks')->nullable()->comment('Late,Early Departure, Late & Early Departure');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sheets');
    }
};
