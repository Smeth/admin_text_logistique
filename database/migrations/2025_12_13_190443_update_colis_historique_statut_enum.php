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
        DB::statement("ALTER TABLE colis_historique MODIFY COLUMN statut_avant ENUM(
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
        ) NULL");
        
        DB::statement("ALTER TABLE colis_historique MODIFY COLUMN statut_apres ENUM(
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
        ) NOT NULL");
        
        // Ensuite migrer les données existantes
        DB::statement("UPDATE colis_historique SET statut_avant = 'emballe' WHERE statut_avant = 'en_attente'");
        DB::statement("UPDATE colis_historique SET statut_avant = 'expedie_port' WHERE statut_avant = 'en_transit'");
        DB::statement("UPDATE colis_historique SET statut_apres = 'emballe' WHERE statut_apres = 'en_attente'");
        DB::statement("UPDATE colis_historique SET statut_apres = 'expedie_port' WHERE statut_apres = 'en_transit'");
        
        // Enfin, modifier l'enum pour retirer les anciens statuts
        DB::statement("ALTER TABLE colis_historique MODIFY COLUMN statut_avant ENUM(
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
        ) NULL");
        
        DB::statement("ALTER TABLE colis_historique MODIFY COLUMN statut_apres ENUM(
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
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migrer les données vers les anciens statuts
        DB::statement("UPDATE colis_historique SET statut_avant = 'en_attente' WHERE statut_avant IN ('emballe', 'expedie_port', 'arrive_aeroport_depart')");
        DB::statement("UPDATE colis_historique SET statut_avant = 'en_transit' WHERE statut_avant IN ('en_vol', 'arrive_aeroport_transit', 'arrive_aeroport_destination', 'en_dedouanement', 'arrive_entrepot')");
        DB::statement("UPDATE colis_historique SET statut_apres = 'en_attente' WHERE statut_apres IN ('emballe', 'expedie_port', 'arrive_aeroport_depart')");
        DB::statement("UPDATE colis_historique SET statut_apres = 'en_transit' WHERE statut_apres IN ('en_vol', 'arrive_aeroport_transit', 'arrive_aeroport_destination', 'en_dedouanement', 'arrive_entrepot')");
        
        // Restaurer les anciens enums
        DB::statement("ALTER TABLE colis_historique MODIFY COLUMN statut_avant ENUM(
            'en_attente',
            'en_transit',
            'livre',
            'retourne'
        ) NULL");
        
        DB::statement("ALTER TABLE colis_historique MODIFY COLUMN statut_apres ENUM(
            'en_attente',
            'en_transit',
            'livre',
            'retourne'
        ) NOT NULL");
    }
};
