<?php

namespace Database\Seeders;

use App\Models\Devise;
use Illuminate\Database\Seeder;

class DeviseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer la devise FCFA comme principale par défaut
        Devise::updateOrCreate(
            ['code' => 'FCFA'],
            [
                'nom' => 'Franc CFA',
                'symbole' => 'FCFA',
                'taux_change' => 1.0000,
                'est_principale' => true,
                'actif' => true,
            ]
        );

        // Créer d'autres devises courantes (optionnel)
        Devise::updateOrCreate(
            ['code' => 'EUR'],
            [
                'nom' => 'Euro',
                'symbole' => '€',
                'taux_change' => 655.9570, // Exemple: 1 EUR = 655.957 FCFA
                'est_principale' => false,
                'actif' => true,
            ]
        );

        Devise::updateOrCreate(
            ['code' => 'USD'],
            [
                'nom' => 'Dollar US',
                'symbole' => '$',
                'taux_change' => 600.0000, // Exemple: 1 USD = 600 FCFA
                'est_principale' => false,
                'actif' => true,
            ]
        );
    }
}

