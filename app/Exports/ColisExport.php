<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ColisExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Numéro de suivi',
            'Client',
            'Poids (kg)',
            'Dimensions',
            'Description',
            'Valeur déclarée',
            'Statut',
            'Date envoi',
            'Date livraison prévue',
            'Agence départ',
            'Agence arrivée',
            'Pays origine',
            'Ville origine',
            'Frais transport',
            'Devise',
            'Payé',
            'Date de création'
        ];
    }

    public function map($coli): array
    {
        return [
            $coli->id,
            $coli->numero_suivi,
            $coli->client->full_name ?? '',
            $coli->poids,
            $coli->dimensions,
            $coli->description_contenu,
            $coli->valeur_declaree,
            $coli->statut,
            $coli->date_envoi ? $coli->date_envoi->format('Y-m-d') : '',
            $coli->date_livraison_prevue ? $coli->date_livraison_prevue->format('Y-m-d') : '',
            $coli->agenceDepart->nom_agence ?? '',
            $coli->agenceArrivee->nom_agence ?? '',
            $coli->pays_origine,
            $coli->ville_origine,
            $coli->frais_transport,
            $coli->devise->nom ?? '',
            $coli->paye ? 'Oui' : 'Non',
            $coli->created_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

