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
                'display_name' => 'Agent',
                'description' => 'Peut créer des colis et ajouter des clients',
            ]
        );
    }
}
