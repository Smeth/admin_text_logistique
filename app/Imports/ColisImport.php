<?php

namespace App\Imports;

use App\Models\Coli;
use App\Models\Client;
use App\Models\Agence;
use App\Models\Devise;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class ColisImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        // Trouver le client par email ou nom
        $client = null;
        if (isset($row['client_email']) || isset($row['Client Email'])) {
            $email = $row['client_email'] ?? $row['Client Email'] ?? '';
            $client = Client::where('email', $email)->first();
        }

        // Trouver les agences
        $agenceDepart = null;
        $agenceArrivee = null;
        if (isset($row['agence_depart']) || isset($row['Agence Depart'])) {
            $nomAgenceDepart = $row['agence_depart'] ?? $row['Agence Depart'] ?? '';
            $agenceDepart = Agence::where('nom_agence', $nomAgenceDepart)->first();
        }
        if (isset($row['agence_arrivee']) || isset($row['Agence Arrivee'])) {
            $nomAgenceArrivee = $row['agence_arrivee'] ?? $row['Agence Arrivee'] ?? '';
            $agenceArrivee = Agence::where('nom_agence', $nomAgenceArrivee)->first();
        }

        // Trouver la devise
        $devise = null;
        if (isset($row['devise']) || isset($row['Devise'])) {
            $codeDevise = $row['devise'] ?? $row['Devise'] ?? '';
            $devise = Devise::where('code', $codeDevise)->orWhere('nom', $codeDevise)->first();
        }

        if (!$client) {
            throw new \Exception('Client introuvable pour le colis : ' . ($row['numero_suivi'] ?? $row['Numero de suivi'] ?? 'N/A'));
        }

        return new Coli([
            'client_id' => $client->id,
            'numero_suivi' => $row['numero_suivi'] ?? $row['Numero de suivi'] ?? '',
            'poids' => $row['poids'] ?? $row['Poids (kg)'] ?? 0,
            'dimensions' => $row['dimensions'] ?? $row['Dimensions'] ?? null,
            'description_contenu' => $row['description'] ?? $row['Description'] ?? null,
            'valeur_declaree' => $row['valeur_declaree'] ?? $row['Valeur declaree'] ?? null,
            'statut' => $row['statut'] ?? $row['Statut'] ?? 'emballe',
            'date_envoi' => $row['date_envoi'] ?? $row['Date envoi'] ?? now(),
            'date_livraison_prevue' => $row['date_livraison_prevue'] ?? $row['Date livraison prevue'] ?? null,
            'agence_depart_id' => $agenceDepart->id ?? null,
            'agence_arrivee_id' => $agenceArrivee->id ?? null,
            'pays_origine' => $row['pays_origine'] ?? $row['Pays origine'] ?? null,
            'ville_origine' => $row['ville_origine'] ?? $row['Ville origine'] ?? null,
            'frais_transport' => $row['frais_transport'] ?? $row['Frais transport'] ?? 0,
            'devise_id' => $devise->id ?? null,
            'paye' => isset($row['paye']) ? ($row['paye'] === 'Oui' || $row['paye'] === true) : false,
        ]);
    }

    public function rules(): array
    {
        return [
            'numero_suivi' => 'required|string|unique:colis,numero_suivi',
            'poids' => 'required|numeric|min:0',
            'date_envoi' => 'required|date',
        ];
    }
}

