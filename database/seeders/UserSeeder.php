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
        // Récupérer le rôle admin (le plus élevé)
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$adminRole) {
            throw new \Exception('Le rôle admin n\'existe pas. Exécutez d\'abord RoleSeeder.');
        }

        // Créer un utilisateur admin par défaut avec le rôle le plus élevé
        User::updateOrCreate(
            ['email' => 'admin@livrango.com'],
            [
                'name' => 'Administrateur',
                'email' => 'admin@livrango.com',
                'password' => Hash::make('admin'),
                'role_id' => $adminRole->id, // Rôle admin = le plus élevé
            ]
        );

        echo "✅ Utilisateur admin créé avec le rôle le plus élevé (admin)\n";
    }
}

