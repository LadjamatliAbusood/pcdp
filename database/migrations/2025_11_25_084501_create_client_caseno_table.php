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
        Schema::create('client_caseno', function (Blueprint $table) {
            $table->id();
             $table->foreignId('client_personal_info_id')
                  ->constrained('client_personal_info')
                  ->onDelete('cascade');

            $table->string('fingerprint_status')->default('Pending'); 
            $table->string('case_no');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_caseno');
    }
};
