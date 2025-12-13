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
        Schema::create('caisses', function (Blueprint $table) {
            $table->id();
            $table->string('nom_caisse');
            $table->foreignId('agence_id')->nullable()->constrained('agences')->onDelete('set null');
            $table->foreignId('responsable_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('solde_initial', 10, 2)->default(0);
            $table->decimal('solde_actuel', 10, 2)->default(0);
            $table->enum('statut', ['ouverte', 'fermee'])->default('fermee');
            $table->dateTime('date_ouverture')->nullable();
            $table->dateTime('date_fermeture')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caisses');
    }
};

