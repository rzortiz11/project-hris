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
        Schema::create('employee_health_benefit_details', function (Blueprint $table) {
            $table->id('employee_health_benefit_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('enrollment_date')->nullable();
            $table->date('coverage_start_date')->nullable();
            $table->date('coverage_end_date')->nullable();
            $table->decimal('monthly_premium', 10, 2)->nullable();
            // $table->decimal('deductible', 10, 2)->nullable();
            // $table->decimal('copay', 10, 2)->nullable();
            // $table->decimal('out_of_pocket_max', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_health_benefit_details');
    }
};
