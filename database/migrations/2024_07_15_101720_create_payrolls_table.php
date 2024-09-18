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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id('payroll_id');
            $table->unsignedBigInteger('pay_period_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('fullname')->nullable();
            $table->string('job_position')->nullable();
            $table->string('reporting_designation')->nullable()->comment('Department');
            $table->string('location')->nullable()->comment('branch');
            $table->string('company')->nullable();
            $table->date('cut_off_from')->nullable();
            $table->date('cut_off_to')->nullable();
            $table->date('cut_off')->nullable();
            $table->integer('day_range')->nullable();
            $table->integer('working_days')->nullable();
            $table->float('regular_overtime_hours')->nullable(); 
            $table->float('rest_day_hours')->nullable();
            $table->float('rest_day_overtime_hours')->nullable(); 
            $table->float('legal_holiday_hours')->nullable();
            $table->float('legal_holiday_overtime_hours')->nullable();
            $table->float('special_holiday_hours')->nullable();
            $table->float('special_holiday_overtime_hours')->nullable();
            $table->integer('absent')->nullable(); // Equivalent to 8 hours
            $table->integer('late_days')->nullable();
            $table->float('late_hours')->nullable(); 
            $table->integer('leave_days')->nullable();
            $table->float('leave_hours')->nullable(); 
            $table->float('time_change_hours')->nullable(); // Time change category
            $table->float('over_time_hours')->nullable(); 
            $table->float('under_time_hours')->nullable(); 
            $table->float('retro_hours')->nullable(); 
            
            $table->decimal('basic_pay', 10, 2)->default(0.00);
            $table->decimal('time_change_pay', 10, 2)->default(0.00);
            $table->decimal('over_time_pay', 10, 2)->default(0.00); // over time with regular pay
            $table->decimal('holiday_pay', 10, 2)->default(0.00); // over time with holiday pay
            $table->decimal('under_time_pay', 10, 2)->default(0.00);
            $table->decimal('allowances_pay', 10, 2)->default(0.00);
            $table->decimal('retro_pay', 10, 2)->default(0.00); // Retro Pay (Retroactive Pay) - Compensation that is owed to an employee for work done in a prior pay period but was not paid correctly
            $table->decimal('bonuses_pay', 10, 2)->default(0.00);

            $table->decimal('total_gross_pay', 10, 2)->default(0.00); // Total salary before deductions
            $table->decimal('sss_contribution', 10, 2)->default(0.00);  // Social Security System (SSS) contribution
            $table->decimal('philhealth_contribution', 10, 2)->default(0.00);  // PhilHealth contribution
            $table->decimal('pagibig_contribution', 10, 2)->default(0.00);  // Pag-IBIG contribution
            $table->decimal('other_deductions', 10, 2)->default(0);  // Any other deductions
            $table->decimal('taxable_income', 10, 2)->default(0.00); // Taxable portion of the salary (total gross pay - contributions)
            $table->decimal('income_tax_withheld', 10, 2)->default(0.00); // Amount of income tax withheld example 30% is the tax of the government / (taxable income)
            $table->decimal('total_net_pay', 10, 2)->default(0.00); // Net pay after all deductions

            $table->decimal('cash_advance', 10, 2)->default(0.00);  
            $table->decimal('adjustment', 10, 2)->default(0.00);

            $table->enum('status', ['pending', 'approved', 'denied', 'void'])->nullable()->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('employee_id');
            $table->index('status');
            $table->index('payroll_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
