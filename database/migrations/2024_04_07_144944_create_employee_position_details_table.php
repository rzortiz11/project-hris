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
        Schema::create('employee_position_details', function (Blueprint $table) {
            $table->id('employee_position_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('job_position')->nullable();
            $table->string('job_category')->nullable();
            $table->string('job_description')->nullable();
            $table->string('joined_designation')->nullable();
            $table->unsignedBigInteger('reporting_person')->nullable();
            $table->string('reporting_designation')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_position_details');
    }
};
