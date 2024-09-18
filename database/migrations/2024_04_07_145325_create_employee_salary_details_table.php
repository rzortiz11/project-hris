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
        Schema::create('employee_salary_details', function (Blueprint $table) {
            $table->id('employee_salary_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('name');
            $table->string('type')->comment('basic-salary,allowance,13 month,14thmonth');
            $table->decimal('daily_amount', 10, 2)->default(0.00);
            $table->decimal('bi_weekly_amount', 10, 2)->default(0.00);
            $table->decimal('monthly_amount', 10, 2)->default(0.00);
            $table->decimal('yearly_amount', 10, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salary_details');
    }
};
