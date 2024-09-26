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
            $table->string('type')->comment('basic,allowance,bonus');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->date('effective_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->boolean('is_taxable')->default(false);
            $table->enum('pay_period', ['weekly', 'bi-weekly', 'monthly', 'annually'])->nullable();
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
