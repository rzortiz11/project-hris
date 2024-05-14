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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id('leave_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('job_position')->nullable();
            $table->string('type')->nullable();
            $table->date('date_filling')->nullable();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->integer('hours')->default(0);
            $table->text('remarks')->nullable();
            $table->boolean('is_paid')->default(0);
            $table->unsignedBigInteger('approver_id');
            $table->date('action_date')->nullable();
            $table->text('disapproved_reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'denied', 'void'])->nullable()->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
