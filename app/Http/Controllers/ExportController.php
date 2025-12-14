<?php

namespace App\Http\Controllers;

use App\Exports\ClientsExport;
use App\Exports\ColisExport;
use App\Exports\TransactionsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Client;
use App\Models\Coli;
use App\Models\Transaction;
use App\Models\Devise;
use App\Models\Tarif;
use App\Models\Agence;
use App\Models\Role;

class ExportController extends Controller
{
    public function index()
    {
        return view('exports.index');
    }

    public function exportClients(Request $request)
    {
        $query = Client::query();
        
        // Filtres optionnels
        if ($request->has('statut') && $request->statut !== '') {
            $query->where('statut', $request->statut);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return Excel::download(new ClientsExport($query), 'clients_' . date('Y-m-d') . '.xlsx');
    }

    public function exportColis(Request $request)
    {
        $query = Coli::with(['client', 'agenceDepart', 'agenceArrivee', 'devise']);
        
        // Filtres optionnels
        if ($request->has('statut') && $request->statut !== '') {
            $query->where('statut', $request->statut);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('date_envoi', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('date_envoi', '<=', $request->date_to);
        }

        return Excel::download(new ColisExport($query), 'colis_' . date('Y-m-d') . '.xlsx');
    }

    public function exportTransactions(Request $request)
    {
        $query = Transaction::with(['caisse', 'devise', 'coli', 'client']);
        
        // Filtres optionnels
        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('date_transaction', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('date_transaction', '<=', $request->date_to);
        }

        return Excel::download(new TransactionsExport($query), 'transactions_' . date('Y-m-d') . '.xlsx');
    }

    public function exportConfigurations()
    {
        $data = [
            'devises' => Devise::all()->toArray(),
            'tarifs' => Tarif::all()->toArray(),
            'agences' => Agence::all()->toArray(),
            'roles' => Role::all()->toArray(),
        ];

        $filename = 'configurations_' . date('Y-m-d') . '.json';
        $filepath = storage_path('app/exports/' . $filename);
        
        // Créer le dossier si nécessaire
        if (!file_exists(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }

        file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return response()->download($filepath)->deleteFileAfterSend(true);
    }
}
