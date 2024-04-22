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
        Schema::create('employee_family_details', function (Blueprint $table) {
            $table->id('employee_family_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('name');
            $table->date('birthdate')->nullable();
            $table->string('occupation')->nullable();
            $table->string('employeer')->nullable();
            $table->string('mobile')->nullable();
            $table->string('address')->nullable();
            $table->string('relationship')->nullable();
            $table->string('anniversary')->nullable()->comment('FOR SPOUSE');
            $table->string('school')->nullable()->comment('FOR CHILDREN');
            $table->boolean('is_alive')->default(1);
            $table->boolean('is_disabled')->default(0);
            $table->boolean('is_medical_entitled')->default(0);
            $table->boolean('is_dependent')->default(0);
            $table->boolean('is_adopted')->default(0)->comment('FOR CHILDREN');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_family_details');
    }
};
