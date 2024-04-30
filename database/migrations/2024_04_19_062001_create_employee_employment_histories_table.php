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
        Schema::create('employee_employment_histories', function (Blueprint $table) {
            $table->id('employment_history_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('company_name')->nullable();
            $table->string('job_title')->nullable();
            $table->string('job_description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_employment_histories');
    }
};
