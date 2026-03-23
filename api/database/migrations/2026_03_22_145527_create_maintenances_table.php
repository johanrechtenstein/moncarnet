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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('kilometrage');
            $table->text('description')->nullable();
            $table->string('facture_url')->nullable();
            $table->boolean('est_obsolete')->default(false);

            // Les relations
            $table->foreignId('categorie_id')->constrained('categories');
            $table->foreignId('car_id')->constrained('cars')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('maintenances')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
