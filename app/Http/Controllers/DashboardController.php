<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Coli;
use App\Models\Transaction;
use App\Models\Caisse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        
        // Revenus du jour (transactions d'entrée d'aujourd'hui)
        $revenusJour = Transaction::where('type', 'entree')
            ->whereDate('date_transaction', today())
            ->sum('montant');

        // Revenus de la semaine (7 derniers jours)
        $revenusSemaine = Transaction::where('type', 'entree')
            ->where('date_transaction', '>=', Carbon::now()->subDays(7)->startOfDay())
            ->sum('montant');

        // Revenus du mois (mois en cours)
        $revenusMois = Transaction::where('type', 'entree')
            ->whereYear('date_transaction', Carbon::now()->year)
            ->whereMonth('date_transaction', Carbon::now()->month)
            ->sum('montant');

        // Dépenses du jour
        $depensesJour = Transaction::where('type', 'sortie')
            ->whereDate('date_transaction', today())
            ->sum('montant');

        // Statistiques sur les caisses
        $caissesOuvertes = Caisse::where('statut', 'ouverte')->count();
        $soldeTotalCaisses = Caisse::where('statut', 'ouverte')->sum('solde_actuel');

        // Colis en retard (date de livraison prévue dépassée et non livrés)
        $colisEnRetard = Coli::where('statut', '!=', 'livre')
            ->whereNotNull('date_livraison_prevue')
            ->whereDate('date_livraison_prevue', '<', today())
            ->count();

        // Colis non payés
        $colisNonPayes = Coli::where('paye', false)->count();

        // Données pour le graphique des revenus (7 derniers jours)
        $revenusParJour = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenus = Transaction::where('type', 'entree')
                ->whereDate('date_transaction', $date->format('Y-m-d'))
                ->sum('montant');
            $revenusParJour[] = [
                'date' => $date->format('d/m'),
                'jour' => $date->format('D'),
                'montant' => $revenus
            ];
        }

        // Derniers clients
        $derniersClients = Client::latest()->take(5)->get();
        
        // Derniers colis
        $derniersColis = Coli::with(['client', 'agenceDepart', 'agenceArrivee'])
            ->latest()
            ->take(5)
            ->get();

        // Colis en retard (pour affichage)
        $colisEnRetardListe = Coli::where('statut', '!=', 'livre')
            ->whereNotNull('date_livraison_prevue')
            ->whereDate('date_livraison_prevue', '<', today())
            ->with('client')
            ->orderBy('date_livraison_prevue', 'asc')
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
            'revenusSemaine',
            'revenusMois',
            'depensesJour',
            'caissesOuvertes',
            'soldeTotalCaisses',
            'colisEnRetard',
            'colisNonPayes',
            'revenusParJour',
            'derniersClients',
            'derniersColis',
            'colisEnRetardListe'
        ));
    }
}
