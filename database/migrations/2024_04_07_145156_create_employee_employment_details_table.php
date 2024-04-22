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
            $table->string('employment_type')->default('probationary');
            $table->json('shift_schedule')->nullable();
            $table->string('status')->default('active');
            $table->boolean('overtime_entitlement')->default(false);
            $table->date('employement_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->enum('employment_status', ['employed', 'terminated', 'resigned', 'other'])->default('employed');
            $table->string('payroll_cycle')->default('Company');
            $table->string('paymet_structure')->default('Company');
            $table->date('termination_date')->nullable();
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
