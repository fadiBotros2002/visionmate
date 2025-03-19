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
        Schema::create('requests', function (Blueprint $table) {
            $table->bigIncrements('request_id');
            $table->unsignedBigInteger('blind_id');
            $table->unsignedBigInteger('volunteer_id')->nullable();
            $table->timestamp('request_time')->useCurrent(); // date of request
            $table->enum('status', ['pending', 'accepted'])->default('pending');
            $table->text('blind_location')->nullable();
            $table->timestamp('accepted_at')->nullable(); // date of response
            $table->timestamps();

            $table->foreign('blind_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('volunteer_id')->references('user_id')->on('users')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
