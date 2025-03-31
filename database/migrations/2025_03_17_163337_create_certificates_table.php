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
        Schema::create('certificates', function (Blueprint $table) {
            $table->bigIncrements('certificate_id');
            $table->unsignedBigInteger('volunteer_id');
            $table->enum('certificate_type', ['helper', 'supporter', 'champion', 'legend']);
            $table->string('certificate_file')->nullable();
            $table->timestamp('awarded_at')->default(now());
            $table->timestamps();

            $table->foreign('volunteer_id')->references('user_id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
