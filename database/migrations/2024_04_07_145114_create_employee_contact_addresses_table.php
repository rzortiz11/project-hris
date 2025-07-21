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
        Schema::create('employee_contact_addresses', function (Blueprint $table) {
            $table->id('employee_contact_address_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('barangay_id')->nullable();
            $table->string('landmark')->nullable();
            $table->string('unit_no')->nullable();
            $table->string('bldg_floor')->nullable();
            $table->string('street')->nullable();
            $table->string('subdivision')->nullable();
            $table->enum('type',['TEMPORARY','PERMANENT'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_contact_addresses');
    }
};
