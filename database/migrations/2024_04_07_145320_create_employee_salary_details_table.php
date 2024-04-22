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
            $table->string('type')->comment('basic salary,transportation allowance, de minimis,13 month');
            $table->integer('basic_amount')->default(0);
            $table->integer('montly_amount')->default(0);
            $table->integer('yearly_amount')->default(0);
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
