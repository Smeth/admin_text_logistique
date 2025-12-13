<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Caisse;
use App\Models\Coli;
use App\Models\Client;
use App\Models\Devise;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['caisse', 'user', 'coli', 'client', 'devise']);

        // Filtrage par agence pour responsable d'agence
        $user = auth()->user();
        if ($user->isResponsableAgence() && $user->agence_id) {
            $query->pourAgence($user->agence_id);
        }

        // Filtre par caisse
        if ($request->has('caisse_id') && $request->caisse_id) {
            $query->where('caisse_id', $request->caisse_id);
        }

        // Filtre par type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filtre par date
        if ($request->has('date_debut') && $request->date_debut) {
            $query->whereDate('date_transaction', '>=', $request->date_debut);
        }

        if ($request->has('date_fin') && $request->date_fin) {
            $query->whereDate('date_transaction', '<=', $request->date_fin);
        }

        // Recherche
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('libelle', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest('date_transaction')->latest('created_at')->paginate(20);
        $caisses = Caisse::all();

        return view('transactions.index', compact('transactions', 'caisses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Filtrer les caisses pour responsable d'agence
        $caisses = Caisse::where('statut', 'ouverte')->get();
        if ($user->isResponsableAgence() && $user->agence_id) {
            $caisses = Caisse::where('statut', 'ouverte')
                ->where('agence_id', $user->agence_id)
                ->get();
        }
        
        $colis = Coli::where('paye', false)->get();
        $clients = Client::all();
        $devises = Devise::where('actif', true)->get();

        return view('transactions.create', compact('caisses', 'colis', 'clients', 'devises'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'caisse_id' => 'required|exists:caisses,id',
            'type' => 'required|in:entree,sortie',
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0.01',
            'devise_id' => 'nullable|exists:devises,id',
            'coli_id' => 'nullable|exists:colis,id',
            'client_id' => 'nullable|exists:clients,id',
            'date_transaction' => 'required|date',
            'description' => 'nullable|string',
            'reference' => 'nullable|string|max:255',
        ]);

        // Vérifier que la caisse est ouverte
        $caisse = Caisse::findOrFail($validated['caisse_id']);
        if (!$caisse->isOuverte()) {
            return back()->withInput()
                ->with('error', 'Impossible d\'enregistrer une transaction sur une caisse fermée.');
        }

        $validated['user_id'] = auth()->id();

        $transaction = Transaction::create($validated);

        // Mettre à jour le solde de la caisse
        $caisse->mettreAJourSolde();

        // Si c'est un paiement de colis, créer aussi un paiement
        if ($validated['type'] === 'entree' && $validated['coli_id']) {
            $coli = Coli::findOrFail($validated['coli_id']);
            
            // Créer le paiement
            $paiement = $coli->paiements()->create([
                'caisse_id' => $validated['caisse_id'],
                'transaction_id' => $transaction->id,
                'montant' => $validated['montant'],
                'devise_id' => $validated['devise_id'],
                'mode_paiement' => $request->input('mode_paiement', 'espece'),
                'date_paiement' => $validated['date_transaction'],
                'user_id' => auth()->id(),
                'notes' => $validated['description'],
            ]);

            // Marquer le colis comme payé
            $coli->update(['paye' => true]);
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction enregistrée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['caisse', 'user', 'coli', 'client', 'devise', 'paiement']);
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $caisse = $transaction->caisse;
        
        // Supprimer le paiement associé si existe
        if ($transaction->paiement) {
            $transaction->paiement->coli->update(['paye' => false]);
            $transaction->paiement->delete();
        }

        $transaction->delete();

        // Mettre à jour le solde de la caisse
        $caisse->mettreAJourSolde();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction supprimée avec succès.');
    }
}

