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
        // future use : to query only approved overtime excluding the disapproved overtime :)
        // lessen the query 
        Schema::create('employee_over_times', function (Blueprint $table) {
            $table->id('employee_over_time_id');
            $table->unsignedBigInteger('over_time_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('date_filling')->nullable();
            $table->date('date_from')->nullable();
            $table->time('time_from')->default('00:00');
            $table->date('date_to')->nullable();
            $table->time('time_to')->default('00:00');
            $table->integer('hours')->default(0);
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->decimal('over_time_rate', 10, 2)->default(0.00);
            $table->decimal('hourly_rate', 10, 2)->default(0.00);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_over_times');
    }
};
