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
        Schema::create('employee_trainings', function (Blueprint $table) {
            $table->id('employee_training_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('training_status_type_id')->nullable();
            $table->unsignedBigInteger('training_type_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->string('course_title')->nullable();
            $table->string('course_url')->nullable();
            $table->string('description')->nullable();
            $table->integer('credit_hours')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_trainings');
    }
};
