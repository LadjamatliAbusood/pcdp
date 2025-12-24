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
        Schema::create('client_fam_info', function (Blueprint $table) {
            $table->id();
            // Foreign Key to the parent client
           $table->foreignId('client_category_case_id')
                  ->constrained('client_category_case')
                  ->onDelete('cascade');
            
            // Family Member Details
            $table->string('fam_img');
            $table->string('nickname')->nullable();
            $table->string('firstname', 225);
            $table->string('middlename', 225)->nullable();
            $table->string('lastname', 225);
            $table->string('extensionname', 225)->nullable();
            $table->unsignedTinyInteger('sex');
            $table->date('birthdate');
            $table->string('civil_status', 225);
            $table->string('relationship', 225);
            $table->unsignedTinyInteger('education_attainment');
            $table->string('skills_and_occupation',225); 
            $table->string('health_status', 225);
            
            // Income (Nullable as per validation)
            $table->string('estimated_income_foriegn', 225)->nullable();
            $table->string('estimated_code_currency', 225)->nullable();
            $table->string('estimated_income_local', 225)->nullable();
            $table->string('estimated_code', 225)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_fam_info');
    }
};
