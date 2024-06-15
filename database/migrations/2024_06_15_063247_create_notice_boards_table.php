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
        Schema::create('notice_boards', function (Blueprint $table) {
            $table->id('notice_board_id');
            $table->string('title');
            $table->longText('description');
            $table->dateTime('publish_at')->nullable();
            $table->boolean('visible')->default(FALSE);
            $table->boolean('active')->nullable();
            $table->json('attachments')->nullable();
            $table->json('users_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notice_boards');
    }
};
