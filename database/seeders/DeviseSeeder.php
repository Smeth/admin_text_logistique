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
        // S'assurer qu'il n'y a qu'une seule devise principale avant de commencer
        $devisesPrincipales = Devise::where('est_principale', true)->get();
        if ($devisesPrincipales->count() > 1) {
            // Garder seulement la première comme principale
            $premiere = $devisesPrincipales->first();
            Devise::where('est_principale', true)
                ->where('id', '!=', $premiere->id)
                ->update(['est_principale' => false]);
        }

        // Créer la devise FCFA comme principale par défaut
        $fcfa = Devise::updateOrCreate(
            ['code' => 'FCFA'],
            [
                'nom' => 'Franc CFA',
                'symbole' => 'FCFA',
                'taux_change' => 1.0000,
                'est_principale' => true,
                'actif' => true,
            ]
        );

        // S'assurer que FCFA est la seule devise principale
        Devise::where('id', '!=', $fcfa->id)
            ->where('est_principale', true)
            ->update(['est_principale' => false]);

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

