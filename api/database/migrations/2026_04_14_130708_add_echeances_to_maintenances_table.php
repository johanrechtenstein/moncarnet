<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            Schema::table('maintenances', function (Blueprint $table) {
        // Le prochain kilométrage
        $table->integer('echeance_km')->nullable()->after('kilometrage');
        
        // La prochaine date limite (pour tes "4 ans")
        $table->date('echeance_date')->nullable()->after('echeance_km');
    });
        });
    }

    
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            Schema::table('maintenances', function (Blueprint $table) {
        $table->dropColumn(['echeance_km', 'echeance_date']);
    });
        });
    }
};
