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
        $user = auth()->user();
        $agenceId = $user->isResponsableAgence() ? $user->agence_id : null;

        // Filtrer les statistiques par agence pour responsable d'agence
        $totalClients = $agenceId 
            ? Client::avecColisAgence($agenceId)->count()
            : Client::count();
            
        $nouveauxClients = $agenceId
            ? Client::avecColisAgence($agenceId)->whereDate('created_at', today())->count()
            : Client::whereDate('created_at', today())->count();
            
        $clientsActifs = $agenceId
            ? Client::avecColisAgence($agenceId)->where('statut', 'actif')->count()
            : Client::where('statut', 'actif')->count();
            
        $colisLivreAujourdhui = $agenceId
            ? Coli::pourAgence($agenceId)->where('statut', 'livre')
                ->whereDate('date_livraison_prevue', today())
                ->count()
            : Coli::where('statut', 'livre')
                ->whereDate('date_livraison_prevue', today())
                ->count();

        // Statistiques supplémentaires
        $totalColis = $agenceId
            ? Coli::pourAgence($agenceId)->count()
            : Coli::count();
            
        $colisEnTransit = $agenceId
            ? Coli::pourAgence($agenceId)->where('statut', 'en_transit')->count()
            : Coli::where('statut', 'en_transit')->count();
            
        $colisEnAttente = $agenceId
            ? Coli::pourAgence($agenceId)->where('statut', 'en_attente')->count()
            : Coli::where('statut', 'en_attente')->count();
        
        // Revenus du jour (transactions d'entrée d'aujourd'hui)
        $revenusJourQuery = Transaction::where('type', 'entree')
            ->whereDate('date_transaction', today());
        if ($agenceId) {
            $revenusJourQuery->pourAgence($agenceId);
        }
        $revenusJour = $revenusJourQuery->sum('montant');

        // Revenus de la semaine (7 derniers jours)
        $revenusSemaineQuery = Transaction::where('type', 'entree')
            ->where('date_transaction', '>=', Carbon::now()->subDays(6)->startOfDay());
        if ($agenceId) {
            $revenusSemaineQuery->pourAgence($agenceId);
        }
        $revenusSemaine = $revenusSemaineQuery->sum('montant');

        // Revenus du mois (mois en cours)
        $revenusMoisQuery = Transaction::where('type', 'entree')
            ->whereYear('date_transaction', Carbon::now()->year)
            ->whereMonth('date_transaction', Carbon::now()->month);
        if ($agenceId) {
            $revenusMoisQuery->pourAgence($agenceId);
        }
        $revenusMois = $revenusMoisQuery->sum('montant');

        // Dépenses du jour
        $depensesJourQuery = Transaction::where('type', 'sortie')
            ->whereDate('date_transaction', today());
        if ($agenceId) {
            $depensesJourQuery->pourAgence($agenceId);
        }
        $depensesJour = $depensesJourQuery->sum('montant');

        // Statistiques sur les caisses
        $caissesOuvertesQuery = Caisse::where('statut', 'ouverte');
        if ($agenceId) {
            $caissesOuvertesQuery->pourAgence($agenceId);
        }
        $caissesOuvertes = $caissesOuvertesQuery->count();
        
        $soldeTotalCaissesQuery = Caisse::where('statut', 'ouverte');
        if ($agenceId) {
            $soldeTotalCaissesQuery->pourAgence($agenceId);
        }
        $soldeTotalCaisses = $soldeTotalCaissesQuery->sum('solde_actuel');

        // Colis en retard (date de livraison prévue dépassée et non livrés)
        $colisEnRetardQuery = Coli::where('statut', '!=', 'livre')
            ->whereNotNull('date_livraison_prevue')
            ->whereDate('date_livraison_prevue', '<', today());
        if ($agenceId) {
            $colisEnRetardQuery->pourAgence($agenceId);
        }
        $colisEnRetard = $colisEnRetardQuery->count();

        // Colis non payés
        $colisNonPayesQuery = Coli::where('paye', false);
        if ($agenceId) {
            $colisNonPayesQuery->pourAgence($agenceId);
        }
        $colisNonPayes = $colisNonPayesQuery->count();

        // Données pour le graphique des revenus (7 derniers jours)
        $revenusParJour = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenusQuery = Transaction::where('type', 'entree')
                ->whereDate('date_transaction', $date->format('Y-m-d'));
            if ($agenceId) {
                $revenusQuery->pourAgence($agenceId);
            }
            $revenus = $revenusQuery->sum('montant');
            $revenusParJour[] = [
                'date' => $date->format('d/m'),
                'jour' => $date->format('D'),
                'montant' => $revenus
            ];
        }

        // Derniers clients
        $derniersClientsQuery = Client::latest();
        if ($agenceId) {
            $derniersClientsQuery->avecColisAgence($agenceId);
        }
        $derniersClients = $derniersClientsQuery->take(5)->get();
        
        // Derniers colis
        $derniersColisQuery = Coli::with(['client', 'agenceDepart', 'agenceArrivee'])->latest();
        if ($agenceId) {
            $derniersColisQuery->pourAgence($agenceId);
        }
        $derniersColis = $derniersColisQuery->take(5)->get();

        // Colis en retard (pour affichage)
        $colisEnRetardListeQuery = Coli::where('statut', '!=', 'livre')
            ->whereNotNull('date_livraison_prevue')
            ->whereDate('date_livraison_prevue', '<', today())
            ->with('client')
            ->orderBy('date_livraison_prevue', 'asc');
        if ($agenceId) {
            $colisEnRetardListeQuery->pourAgence($agenceId);
        }
        $colisEnRetardListe = $colisEnRetardListeQuery->take(5)->get();

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
