<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrateur',
                'description' => 'Accès complet à toutes les fonctionnalités',
            ]
        );

        Role::updateOrCreate(
            ['name' => 'agent'],
            [
                'display_name' => 'Agent de réception',
                'description' => 'Peut créer des colis et ajouter des clients',
            ]
        );

        Role::updateOrCreate(
            ['name' => 'responsable_agence'],
            [
                'display_name' => 'Responsable d\'Agence',
                'description' => 'Gère uniquement les données de son agence (caisses, transactions, clients, colis)',
            ]
        );

        Role::updateOrCreate(
            ['name' => 'superviseur'],
            [
                'display_name' => 'Superviseur',
                'description' => 'Supervision opérationnelle complète sans gestion financière ni paramètres',
            ]
        );
    }
}
