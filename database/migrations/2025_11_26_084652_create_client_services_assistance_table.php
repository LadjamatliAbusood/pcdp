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
        Schema::create('client_services_assistance', function (Blueprint $table) {
            $table->id();
            // Foreign Key to the parent client
      $table->foreignId('client_category_case_id')
                  ->constrained('client_category_case')
                  ->onDelete('cascade');
            // Recommendation Details
            $table->json('interventions_needed'); 
            $table->string('referred_to'); 
            $table->timestamps();

           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_services_assistance');
    }
};
