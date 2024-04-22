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
        Schema::create('employees', function (Blueprint $table) {
            $table->id('employee_id');
            $table->string('employee_reference')->unique();
            $table->unsignedBigInteger('user_id');
            $table->string('title')->nullable();
            $table->string('gender', 20)->nullable();
            $table->date('birthdate')->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable();
            $table->string('picture')->nullable();
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
        Schema::dropIfExists('employees');
    }
};
