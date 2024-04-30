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
        Schema::create('employee_education', function (Blueprint $table) {
            $table->id('employee_education_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('school')->nullable();
            $table->string('course')->nullable();
            $table->string('degree')->nullable();
            $table->date('year_started')->nullable();
            $table->date('year_end')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_education');
    }
};
