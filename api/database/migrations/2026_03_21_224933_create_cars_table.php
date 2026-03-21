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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('marque', 100); // VARCHAR(100) NOT NULL
            $table->string('modele', 100); // VARCHAR(100) NOT NULL
            $table->string('immatriculation', 20)->unique(); // UNIQUE NOT NULL
            $table->string('image_url', 255)->nullable(); // Optionnel selon ton SQL
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
