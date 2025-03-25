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
            $table->timestamp('request_time')->useCurrent();
            $table->enum('status', ['pending', 'accepted'])->default('pending');
            $table->double('blind_latitude', 15, 8)->nullable();   // double
            $table->double('blind_longitude', 15, 8)->nullable();  // double
            $table->string('blind_location')->nullable();  // string لتخزين الموقع النصي
            $table->timestamp('accepted_at')->nullable();
            $table->boolean('is_rated')->default(false);
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
