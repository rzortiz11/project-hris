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
        Schema::create('over_time_requests', function (Blueprint $table) {
            $table->id('over_time_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('type')->nullable(); // type of over time regular,special,double pay,tripple pay etc. 
            $table->string('job_position')->nullable();
            $table->date('date_filling')->nullable();
            $table->date('date_from')->nullable();
            $table->time('time_from')->default('00:00');
            $table->date('date_to')->nullable();
            $table->time('time_to')->default('00:00');
            $table->integer('hours')->default(0);
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
        Schema::dropIfExists('over_time_requests');
    }
};
