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
        Schema::create('employee_leave_balances', function (Blueprint $table) {
            $table->id('leave_balance_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('type')->nullable();
            $table->string('description')->nullable();
            $table->integer('balance')->default(0);
            $table->integer('used_balance')->default(0);
            $table->integer('remaining_balance')->default(0);
            $table->boolean('is_paid')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leave_balances');
    }
};
