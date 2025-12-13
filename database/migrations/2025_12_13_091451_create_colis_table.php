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
        Schema::create('colis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('numero_suivi')->unique();
            $table->decimal('poids', 8, 2);
            $table->string('dimensions')->nullable();
            $table->text('description_contenu')->nullable();
            $table->decimal('valeur_declaree', 10, 2)->nullable();
            $table->enum('statut', ['en_attente', 'en_transit', 'livre', 'retourne'])->default('en_attente');
            $table->date('date_envoi');
            $table->date('date_livraison_prevue')->nullable();
            $table->foreignId('agence_depart_id')->constrained('agences')->onDelete('restrict');
            $table->foreignId('agence_arrivee_id')->constrained('agences')->onDelete('restrict');
            $table->foreignId('transporteur_id')->nullable()->constrained('entreprises_transporteurs')->onDelete('set null');
            $table->decimal('frais_transport', 10, 2);
            $table->boolean('paye')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colis');
    }
};
