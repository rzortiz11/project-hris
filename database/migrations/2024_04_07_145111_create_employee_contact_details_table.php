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
        Schema::create('employee_contact_details', function (Blueprint $table) {
            $table->id('employee_contact_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('mobile')->nullable();
            $table->string('secondary_mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('secondary_email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('secondary_telephone')->nullable();
            $table->string('facebook_profile')->nullable();
            $table->string('linkedIn_profile')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_contact_details');
    }
};
