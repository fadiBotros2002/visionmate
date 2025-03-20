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
        Schema::create('ratings', function (Blueprint $table) {
                $table->bigIncrements('rating_id');
                $table->unsignedBigInteger('blind_id');
                $table->unsignedBigInteger('volunteer_id');
                $table->unsignedBigInteger('request_id');
                $table->integer('rating')->checkBetween([0, 5]);
                //$table->text('feedback')->nullable();
                $table->timestamps();

                $table->foreign('blind_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('volunteer_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('request_id')->references('request_id')->on('requests')->onDelete('cascade');

            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
