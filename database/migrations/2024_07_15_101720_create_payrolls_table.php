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
            $table->unsignedBigInteger('employee_id');
            $table->string('fullname')->nullable();
            $table->string('job_position')->nullable();
            $table->string('reporting_designation')->nullable()->comment('Department');
            $table->string('location')->nullable()->comment('branch');
            $table->string('company')->nullable();
            $table->integer('cut_off')->nullable();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->integer('day_range')->nullable();
            $table->integer('working_days')->nullable();
            $table->integer('with_sundays')->nullable();
            $table->integer('absent')->nullable();
            $table->integer('late')->nullable();

            $table->timestamps();
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
