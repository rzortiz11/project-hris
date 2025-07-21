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
        Schema::create('time_change_requests', function (Blueprint $table) {
            $table->id('time_change_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('type')->nullable();
            $table->date('date_filling')->nullable();
            $table->time('old_time_in')->nullable();
            $table->time('old_time_out')->nullable();
            $table->time('new_time_in')->nullable();
            $table->time('new_time_out')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('time_change_requests');
    }
};
