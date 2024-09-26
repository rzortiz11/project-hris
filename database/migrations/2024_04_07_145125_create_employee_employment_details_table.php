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
        Schema::create('employee_employment_details', function (Blueprint $table) {
            $table->id('employee_employment_id');
            $table->unsignedBigInteger('employee_id');
            $table->enum('employment_type',['PROBATION','REGULAR'])->default('PROBATION')->nullable();
            $table->enum('employment_category',['PARTTIME','FULLTIME','THIRDPARTY'])->nullable();
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->boolean('overtime_entitlement')->default(false)->nullable();
            $table->date('employement_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->enum('employment_status', ['EMPLOYED', 'TERMINATED', 'RESIGNED', 'SEPERATED'])->default('EMPLOYED')->nullable();
            $table->string('payroll_cycle')->default('Company')->nullable();
            $table->string('payment_structure')->default('Company')->nullable();
            $table->string('company')->nullable();
            $table->enum('work_arrangement',['ONSITE','WFH','HYBRID'])->nullable();
            $table->date('termination_date')->nullable();
            $table->date('seperation_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_employment_details');
    }
};
