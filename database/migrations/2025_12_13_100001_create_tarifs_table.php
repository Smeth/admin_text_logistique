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
        Schema::create('tarifs', function (Blueprint $table) {
            $table->id();
            $table->string('nom_tarif'); // Standard, Express, Premium, etc.
            $table->text('description')->nullable();
            $table->decimal('prix_par_kilo', 10, 2); // Prix de base par kilogramme
            $table->decimal('prix_minimum', 10, 2); // Prix minimum à appliquer
            $table->decimal('prix_maximum', 10, 2)->nullable(); // Prix maximum (optionnel)
            $table->foreignId('agence_depart_id')->nullable()->constrained('agences')->onDelete('set null');
            $table->foreignId('agence_arrivee_id')->nullable()->constrained('agences')->onDelete('set null');
            $table->foreignId('transporteur_id')->nullable()->constrained('entreprises_transporteurs')->onDelete('set null');
            $table->date('date_debut')->nullable(); // Date de début de validité
            $table->date('date_fin')->nullable(); // Date de fin de validité
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifs');
    }
};

