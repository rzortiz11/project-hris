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
        Schema::create('employee_issued_items', function (Blueprint $table) {
            $table->id('employee_issued_item_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('item_type')->nullable();
            $table->string('item_name')->nullable();
            $table->string('item_model')->nullable();
            $table->date('issued_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_issued_items');
    }
};
