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
        Schema::create('client_fingerprints', function (Blueprint $table) {
            $table->id();
           $table->foreignId('client_category_case_id')
                  ->constrained('client_category_case')
                  ->onDelete('cascade');
         
            $table->string('remark')->nullable();
           // LEFT HAND (Thumb to Pinky)
            $table->binary('left_thumb')->nullable();
            $table->binary('left_index')->nullable();
            $table->binary('left_middle')->nullable();
            $table->binary('left_ring')->nullable();
            $table->binary('left_pinky')->nullable();

            // RIGHT HAND (Thumb to Pinky)
            $table->binary('right_thumb')->nullable();
            $table->binary('right_index')->nullable();
            $table->binary('right_middle')->nullable();
            $table->binary('right_ring')->nullable();
            $table->binary('right_pinky')->nullable();

            $table->timestamps();

           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_fingerprints');
    }
};
