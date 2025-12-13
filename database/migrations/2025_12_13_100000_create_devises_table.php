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
        Schema::create('devises', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // FCFA, EUR, USD
            $table->string('nom'); // Franc CFA, Euro, Dollar
            $table->string('symbole', 10); // FCFA, €, $
            $table->decimal('taux_change', 10, 4)->default(1.0000); // Taux par rapport à la devise principale
            $table->boolean('est_principale')->default(false); // Une seule devise principale
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devises');
    }
};

