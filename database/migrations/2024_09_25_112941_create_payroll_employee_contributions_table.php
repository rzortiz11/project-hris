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
        Schema::create('payroll_employee_contributions', function (Blueprint $table) {
            $table->id('contribution_id');
            $table->unsignedBigInteger('payroll_id');
            $table->decimal('sss_contribution', 10, 2)->default(0.00); 
            $table->decimal('philhealth_contribution', 10, 2)->default(0.00);
            $table->decimal('pagibig_contribution', 10, 2)->default(0.00);  
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_employee_contributions');
    }
};
