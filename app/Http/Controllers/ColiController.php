<?php

namespace App\Http\Controllers;

use App\Models\Agence;
use App\Models\Client;
use App\Models\Coli;
use App\Models\EntrepriseTransporteur;
use Illuminate\Http\Request;

class ColiController extends Controller
{
    public function index(Request $request)
    {
        $query = Coli::with(['client', 'agenceDepart', 'agenceArrivee', 'transporteur']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_suivi', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }

        $colis = $query->latest()->paginate(15);
        return view('colis.index', compact('colis'));
    }

    public function create()
    {
        $clients = Client::where('statut', 'actif')->orderBy('nom')->get();
        $agences = Agence::orderBy('nom_agence')->get();
        $transporteurs = EntrepriseTransporteur::where('statut', 'actif')->orderBy('nom_entreprise')->get();
        
        return view('colis.create', compact('clients', 'agences', 'transporteurs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'numero_suivi' => 'required|string|unique:colis,numero_suivi',
            'poids' => 'required|numeric|min:0',
            'dimensions' => 'nullable|string',
            'description_contenu' => 'nullable|string',
            'valeur_declaree' => 'nullable|numeric|min:0',
            'statut' => 'required|in:en_attente,en_transit,livre,retourne',
            'date_envoi' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_envoi',
            'agence_depart_id' => 'required|exists:agences,id',
            'agence_arrivee_id' => 'required|exists:agences,id',
            'transporteur_id' => 'nullable|exists:entreprises_transporteurs,id',
            'frais_transport' => 'required|numeric|min:0',
            'paye' => 'boolean',
        ]);

        Coli::create($validated);

        return redirect()->route('colis.index')
            ->with('success', 'Colis créé avec succès.');
    }

    public function show(Coli $coli)
    {
        $coli->load(['client', 'agenceDepart', 'agenceArrivee', 'transporteur']);
        return view('colis.show', compact('coli'));
    }

    public function edit(Coli $coli)
    {
        $clients = Client::where('statut', 'actif')->orderBy('nom')->get();
        $agences = Agence::orderBy('nom_agence')->get();
        $transporteurs = EntrepriseTransporteur::where('statut', 'actif')->orderBy('nom_entreprise')->get();
        
        return view('colis.edit', compact('coli', 'clients', 'agences', 'transporteurs'));
    }

    public function update(Request $request, Coli $coli)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'numero_suivi' => 'required|string|unique:colis,numero_suivi,' . $coli->id,
            'poids' => 'required|numeric|min:0',
            'dimensions' => 'nullable|string',
            'description_contenu' => 'nullable|string',
            'valeur_declaree' => 'nullable|numeric|min:0',
            'statut' => 'required|in:en_attente,en_transit,livre,retourne',
            'date_envoi' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_envoi',
            'agence_depart_id' => 'required|exists:agences,id',
            'agence_arrivee_id' => 'required|exists:agences,id',
            'transporteur_id' => 'nullable|exists:entreprises_transporteurs,id',
            'frais_transport' => 'required|numeric|min:0',
            'paye' => 'boolean',
        ]);

        $coli->update($validated);

        return redirect()->route('colis.index')
            ->with('success', 'Colis mis à jour avec succès.');
    }

    public function destroy(Coli $coli)
    {
        $coli->delete();
        return redirect()->route('colis.index')
            ->with('success', 'Colis supprimé avec succès.');
    }
}
