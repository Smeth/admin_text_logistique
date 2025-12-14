<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
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
            'Type',
            'Libellé',
            'Montant',
            'Devise',
            'Caisse',
            'Colis',
            'Client',
            'Utilisateur',
            'Date transaction',
            'Description',
            'Référence',
            'Date de création'
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->type === 'entree' ? 'Entrée' : 'Sortie',
            $transaction->libelle,
            $transaction->montant,
            $transaction->devise->nom ?? '',
            $transaction->caisse->nom_caisse ?? '',
            $transaction->coli->numero_suivi ?? '',
            $transaction->client->full_name ?? '',
            $transaction->user->name ?? '',
            $transaction->date_transaction ? $transaction->date_transaction->format('Y-m-d') : '',
            $transaction->description,
            $transaction->reference,
            $transaction->created_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

