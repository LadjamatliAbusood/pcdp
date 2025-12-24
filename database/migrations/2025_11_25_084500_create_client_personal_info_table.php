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
        Schema::create('client_personal_info', function (Blueprint $table) {
            $table->id();
            // Personal Info
            $table->string('nickname')->nullable();
            $table->string('firstname', 225);
            $table->string('middlename', 225)->nullable();
            $table->string('lastname', 225);
            $table->string('extensionname', 225)->nullable();
            $table->unsignedTinyInteger('sex');
            $table->date('birthdate');
           $table->integer('age');
            $table->string('birth_place', 225);
            
            // Birth Registration
            $table->enum('birth_registered_with_local', ['Yes', 'No']);
            $table->string('registered_with_local_where', 225)->nullable();
            $table->string('registered_with_local_reason_why', 225)->nullable();
            
            // Other Info
            $table->string('civil_status', 225);
            $table->string('religion', 225);
            $table->string('dialect', 225); 

            // PH Address
            $table->string('address_in_ph_region', 225);
            $table->string('address_in_ph_province', 225);
            $table->string('address_in_ph_city', 225);
            $table->string('address_in_ph_brgy', 225);
            $table->string('address_in_ph_street', 225)->nullable();
            $table->string('address_in_ph_house_no', 225)->nullable();
            $table->string('address_in_malaysia', 225);

            // Education & Eligibility
            $table->unsignedTinyInteger('education_attainment');
            $table->string('eligibility', 225)->nullable();
            $table->date('eligibility_date_acquired')->nullable();
            $table->string('skills', 225); 

            // Income
            $table->string('estimated_income_foriegn', 225);
            $table->string('estimated_code_currency', 225)->nullable();
            $table->string('estimated_income_local', 225);
            $table->string('estimated_code', 225)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_personal_info');
    }
};
