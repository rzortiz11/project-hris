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
        Schema::create('time_logs', function (Blueprint $table) {
            $table->id('time_log_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('date')->nullable();
            $table->string('day')->nullable();
            $table->string('type')->nullable();
            $table->time('time')->nullable();
            $table->string('location')->nullable()->comment('WFH,OFFICE,ONFIELD');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_logs');
    }
};
