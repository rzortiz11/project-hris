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
        Schema::create('pay_periods', function (Blueprint $table) {
            $table->id('pay_period_id');
            $table->enum('type', ['weekly', 'biweekly', 'monthly'])->nullable();
            $table->date('start_date'); 
            $table->date('end_date'); 
            $table->date('cut_off_date'); // deadline when the payroll/payslip will be release available to employee
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_periods');
    }
};
