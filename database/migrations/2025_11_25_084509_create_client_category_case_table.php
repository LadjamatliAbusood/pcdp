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
        Schema::create('client_category_case', function (Blueprint $table) {
            $table->id();
             $table->foreignId('client_caseno_id')
                  ->constrained('client_caseno')
                  ->onDelete('cascade');
                  
            $table->foreignId('client_category_id')
                  ->constrained('clients_category')
                  ->onDelete('cascade');
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_category_case');
    }
};
