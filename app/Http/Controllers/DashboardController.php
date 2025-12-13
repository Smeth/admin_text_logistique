<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Coli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClients = Client::count();
        $nouveauxClients = Client::whereDate('created_at', today())->count();
        $clientsActifs = Client::where('statut', 'actif')->count();
        $colisLivreAujourdhui = Coli::where('statut', 'livre')
            ->whereDate('date_livraison_prevue', today())
            ->count();

        // Statistiques supplémentaires
        $totalColis = Coli::count();
        $colisEnTransit = Coli::where('statut', 'en_transit')->count();
        $colisEnAttente = Coli::where('statut', 'en_attente')->count();
        
        // Revenus du jour (colis payés)
        $revenusJour = Coli::where('paye', true)
            ->whereDate('created_at', today())
            ->sum('frais_transport');

        // Derniers clients
        $derniersClients = Client::latest()->take(5)->get();
        
        // Derniers colis
        $derniersColis = Coli::with(['client', 'agenceDepart', 'agenceArrivee'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalClients',
            'nouveauxClients',
            'clientsActifs',
            'colisLivreAujourdhui',
            'totalColis',
            'colisEnTransit',
            'colisEnAttente',
            'revenusJour',
            'derniersClients',
            'derniersColis'
        ));
    }
}
