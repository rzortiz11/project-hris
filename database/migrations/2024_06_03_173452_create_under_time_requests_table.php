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
        Schema::create('under_time_requests', function (Blueprint $table) {
            $table->id('under_time_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('type')->nullable();
            $table->date('date_filling')->nullable();
            $table->date('date')->nullable(); // when to undertime
            $table->time('time_out')->default('00:00'); // time_out for undertime
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
        Schema::dropIfExists('under_time_requests');
    }
};
