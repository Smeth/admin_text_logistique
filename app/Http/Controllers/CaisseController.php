<?php

namespace App\Http\Controllers;

use App\Models\Caisse;
use App\Models\Agence;
use App\Models\User;
use Illuminate\Http\Request;

class CaisseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Caisse::with(['agence', 'responsable']);

        // Recherche
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nom_caisse', 'like', "%{$search}%");
        }

        // Filtre par statut
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }

        // Filtre par agence
        if ($request->has('agence_id') && $request->agence_id) {
            $query->where('agence_id', $request->agence_id);
        }

        $caisses = $query->latest()->paginate(15);
        $agences = Agence::all();

        return view('caisses.index', compact('caisses', 'agences'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $agences = Agence::all();
        $users = User::all();
        return view('caisses.create', compact('agences', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_caisse' => 'required|string|max:255',
            'agence_id' => 'nullable|exists:agences,id',
            'responsable_id' => 'nullable|exists:users,id',
            'solde_initial' => 'required|numeric|min:0',
            'statut' => 'required|in:ouverte,fermee',
            'notes' => 'nullable|string',
        ]);

        $validated['solde_actuel'] = $validated['solde_initial'];
        
        // Gérer les dates selon le statut
        if ($validated['statut'] === 'ouverte') {
            $validated['date_ouverture'] = now();
            $validated['date_fermeture'] = null;
        } else {
            $validated['date_ouverture'] = null;
            $validated['date_fermeture'] = null;
        }

        Caisse::create($validated);

        return redirect()->route('caisses.index')
            ->with('success', 'Caisse créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Caisse $caisse)
    {
        $caisse->load(['agence', 'responsable', 'transactions.user', 'paiements']);
        
        // Calculer les statistiques
        $entreesAujourdhui = $caisse->transactions()
            ->where('type', 'entree')
            ->whereDate('date_transaction', today())
            ->sum('montant');
        
        $sortiesAujourdhui = $caisse->transactions()
            ->where('type', 'sortie')
            ->whereDate('date_transaction', today())
            ->sum('montant');
        
        $dernieresTransactions = $caisse->transactions()
            ->with(['user', 'coli', 'client'])
            ->latest()
            ->take(10)
            ->get();

        return view('caisses.show', compact('caisse', 'entreesAujourdhui', 'sortiesAujourdhui', 'dernieresTransactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Caisse $caisse)
    {
        $agences = Agence::all();
        $users = User::all();
        return view('caisses.edit', compact('caisse', 'agences', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Caisse $caisse)
    {
        $validated = $request->validate([
            'nom_caisse' => 'required|string|max:255',
            'agence_id' => 'nullable|exists:agences,id',
            'responsable_id' => 'nullable|exists:users,id',
            'solde_initial' => 'required|numeric|min:0',
            'statut' => 'required|in:ouverte,fermee',
            'notes' => 'nullable|string',
        ]);

        // Si le solde initial change, recalculer le solde actuel
        if ($caisse->solde_initial != $validated['solde_initial']) {
            $difference = $validated['solde_initial'] - $caisse->solde_initial;
            $validated['solde_actuel'] = $caisse->solde_actuel + $difference;
        }

        // Gérer le changement de statut
        $ancienStatut = $caisse->statut;
        $nouveauStatut = $validated['statut'];

        if ($ancienStatut !== $nouveauStatut) {
            if ($nouveauStatut === 'ouverte') {
                // Si on passe de fermée à ouverte
                $validated['date_ouverture'] = now();
                $validated['date_fermeture'] = null;
            } else {
                // Si on passe d'ouverte à fermée
                $validated['date_fermeture'] = now();
                // Recalculer le solde avant fermeture
                $caisse->mettreAJourSolde();
                $validated['solde_actuel'] = $caisse->solde_actuel;
            }
        }

        $caisse->update($validated);

        return redirect()->route('caisses.index')
            ->with('success', 'Caisse mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Caisse $caisse)
    {
        if ($caisse->transactions()->count() > 0) {
            return redirect()->route('caisses.index')
                ->with('error', 'Impossible de supprimer une caisse qui contient des transactions.');
        }

        $caisse->delete();

        return redirect()->route('caisses.index')
            ->with('success', 'Caisse supprimée avec succès.');
    }

    /**
     * Ouvrir une caisse
     */
    public function ouvrir(Caisse $caisse)
    {
        if ($caisse->isOuverte()) {
            return redirect()->route('caisses.show', $caisse)
                ->with('error', 'Cette caisse est déjà ouverte.');
        }

        $caisse->ouvrir();

        return redirect()->route('caisses.show', $caisse)
            ->with('success', 'Caisse ouverte avec succès.');
    }

    /**
     * Fermer une caisse
     */
    public function fermer(Caisse $caisse)
    {
        if (!$caisse->isOuverte()) {
            return redirect()->route('caisses.show', $caisse)
                ->with('error', 'Cette caisse est déjà fermée.');
        }

        $caisse->fermer();

        return redirect()->route('caisses.show', $caisse)
            ->with('success', 'Caisse fermée avec succès. Solde actuel: ' . number_format($caisse->solde_actuel, 0, ',', ' ') . ' FCFA');
    }
}

