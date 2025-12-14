<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class ClientsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        return new Client([
            'nom' => $row['nom'] ?? $row['Nom'] ?? '',
            'prenom' => $row['prenom'] ?? $row['Prenom'] ?? null,
            'email' => $row['email'] ?? $row['Email'] ?? '',
            'telephone' => $row['telephone'] ?? $row['Telephone'] ?? '',
            'adresse' => $row['adresse'] ?? $row['Adresse'] ?? '',
            'type' => $row['type'] ?? $row['Type'] ?? 'particulier',
            'statut' => $row['statut'] ?? $row['Statut'] ?? 'actif',
            'notes' => $row['notes'] ?? $row['Notes'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'telephone' => 'required|string|max:255',
            'adresse' => 'required|string',
            'type' => 'nullable|in:particulier,entreprise',
            'statut' => 'nullable|in:actif,inactif',
        ];
    }
}

