<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // D'abord modifier l'enum pour inclure tous les nouveaux statuts (y compris les anciens temporairement)
        DB::statement("ALTER TABLE colis MODIFY COLUMN statut ENUM(
            'en_attente',
            'en_transit',
            'emballe',
            'expedie_port',
            'arrive_aeroport_depart',
            'en_vol',
            'arrive_aeroport_transit',
            'arrive_aeroport_destination',
            'en_dedouanement',
            'arrive_entrepot',
            'livre',
            'retourne'
        ) DEFAULT 'emballe'");
        
        // Ensuite migrer les données existantes
        DB::statement("UPDATE colis SET statut = 'emballe' WHERE statut = 'en_attente'");
        DB::statement("UPDATE colis SET statut = 'expedie_port' WHERE statut = 'en_transit'");
        
        // Enfin, modifier l'enum pour retirer les anciens statuts
        DB::statement("ALTER TABLE colis MODIFY COLUMN statut ENUM(
            'emballe',
            'expedie_port',
            'arrive_aeroport_depart',
            'en_vol',
            'arrive_aeroport_transit',
            'arrive_aeroport_destination',
            'en_dedouanement',
            'arrive_entrepot',
            'livre',
            'retourne'
        ) DEFAULT 'emballe'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migrer les données vers les anciens statuts
        DB::statement("UPDATE colis SET statut = 'en_attente' WHERE statut IN ('emballe', 'expedie_port', 'arrive_aeroport_depart')");
        DB::statement("UPDATE colis SET statut = 'en_transit' WHERE statut IN ('en_vol', 'arrive_aeroport_transit', 'arrive_aeroport_destination', 'en_dedouanement', 'arrive_entrepot')");
        
        // Restaurer l'ancien enum
        DB::statement("ALTER TABLE colis MODIFY COLUMN statut ENUM(
            'en_attente',
            'en_transit',
            'livre',
            'retourne'
        ) DEFAULT 'en_attente'");
    }
};
