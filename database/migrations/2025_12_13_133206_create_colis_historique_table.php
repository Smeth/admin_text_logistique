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
        Schema::create('colis_historique', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coli_id')->constrained('colis')->onDelete('cascade');
            $table->enum('statut_avant', ['en_attente', 'en_transit', 'livre', 'retourne'])->nullable();
            $table->enum('statut_apres', ['en_attente', 'en_transit', 'livre', 'retourne']);
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->text('commentaire')->nullable();
            $table->string('localisation')->nullable(); // Où se trouve le colis à ce moment
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colis_historique');
    }
};
