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
        Schema::create('client_assessment', function (Blueprint $table) {
            $table->id();
            // Foreign Key to the parent client
          $table->foreignId('client_category_case_id')
                  ->constrained('client_category_case')
                  ->onDelete('cascade');
            
            // Assessment Details
            $table->string('typeofclient', 225);
            $table->string('id_presented', 225);
            $table->string('length_stay_in_malaysia', 225);
              $table->unsignedTinyInteger('length_stay_in_malaysia_options');
            $table->unsignedInteger('additional_length_option_if_with_years')->nullable();
        $table->unsignedTinyInteger('length_value_if_with_years')->nullable();
              

  
            $table->string('client_went_malaysia', 225);
            $table->string('valid_paper_type', 225)->nullable();
            
            $table->enum('client_employeed', ['Yes', 'No']);
            
            // Employment Details (Required if client_employeed is Yes)
            $table->string('nature_of_work', 225)->nullable();
            $table->string('position_title', 225)->nullable();
            $table->string('name_and_address_of_employee', 225)->nullable();
            
            $table->string('client_repatriated', 225)->nullable();
            
            // Travel Documents
            $table->string('travel_document_no', 225)->nullable();
            $table->string('issued_by', 225)->nullable();
            $table->date('date_issued')->nullable();;
            $table->string('passport_ic_no', 225)->nullable();
            
            // Problem & Plan
            $table->string('client_problem',255); 
            $table->enum('client_plan', ['return_to_malaysia', 'remain_in_the_ph']);
            $table->string('client_reason', 225)->nullable(); 
            $table->string('client_employment', 225)->nullable(); 

            // Contact Person
            $table->string('contact_person_fullname', 225);
            $table->string('contact_person_phonenumber', 225);
            $table->string('contact_person_relationship', 225);
            $table->timestamps();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_assessment');
    }
};
