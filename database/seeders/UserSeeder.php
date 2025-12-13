<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $agentRole = Role::where('name', 'agent')->first();

        // Créer un utilisateur admin par défaut
        User::updateOrCreate(
            ['email' => 'admin@gesttransport.com'],
            [
                'name' => 'Administrateur',
                'email' => 'admin@gesttransport.com',
                'password' => Hash::make('password'),
                'role_id' => $adminRole?->id,
            ]
        );

        // Créer un utilisateur agent par défaut (optionnel)
        User::updateOrCreate(
            ['email' => 'agent@gesttransport.com'],
            [
                'name' => 'Agent',
                'email' => 'agent@gesttransport.com',
                'password' => Hash::make('password'),
                'role_id' => $agentRole?->id,
            ]
        );
    }
}

