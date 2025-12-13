<?php

namespace App\Http\Controllers;

use App\Models\Agence;
use App\Models\EntrepriseTransporteur;
use App\Models\Tarif;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    public function index(Request $request)
    {
        $query = Tarif::with(['agenceDepart', 'agenceArrivee', 'transporteur']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom_tarif', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('actif')) {
            $query->where('actif', $request->actif);
        }

        $tarifs = $query->latest()->paginate(15);
        return view('tarifs.index', compact('tarifs'));
    }

    public function create()
    {
        $agences = Agence::orderBy('nom_agence')->get();
        $transporteurs = EntrepriseTransporteur::where('statut', 'actif')->orderBy('nom_entreprise')->get();
        
        return view('tarifs.create', compact('agences', 'transporteurs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_tarif' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix_par_kilo' => 'required|numeric|min:0',
            'prix_minimum' => 'required|numeric|min:0',
            'prix_maximum' => 'nullable|numeric|min:0|gte:prix_minimum',
            'agence_depart_id' => 'nullable|exists:agences,id',
            'agence_arrivee_id' => 'nullable|exists:agences,id',
            'transporteur_id' => 'nullable|exists:entreprises_transporteurs,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'actif' => 'boolean',
        ]);

        Tarif::create($validated);

        return redirect()->route('tarifs.index')
            ->with('success', 'Tarif créé avec succès.');
    }

    public function show(Tarif $tarif)
    {
        $tarif->load(['agenceDepart', 'agenceArrivee', 'transporteur', 'colis']);
        return view('tarifs.show', compact('tarif'));
    }

    public function edit(Tarif $tarif)
    {
        $agences = Agence::orderBy('nom_agence')->get();
        $transporteurs = EntrepriseTransporteur::where('statut', 'actif')->orderBy('nom_entreprise')->get();
        
        return view('tarifs.edit', compact('tarif', 'agences', 'transporteurs'));
    }

    public function update(Request $request, Tarif $tarif)
    {
        $validated = $request->validate([
            'nom_tarif' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix_par_kilo' => 'required|numeric|min:0',
            'prix_minimum' => 'required|numeric|min:0',
            'prix_maximum' => 'nullable|numeric|min:0|gte:prix_minimum',
            'agence_depart_id' => 'nullable|exists:agences,id',
            'agence_arrivee_id' => 'nullable|exists:agences,id',
            'transporteur_id' => 'nullable|exists:entreprises_transporteurs,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'actif' => 'boolean',
        ]);

        $tarif->update($validated);

        return redirect()->route('tarifs.index')
            ->with('success', 'Tarif mis à jour avec succès.');
    }

    public function destroy(Tarif $tarif)
    {
        if ($tarif->colis()->count() > 0) {
            return redirect()->route('tarifs.index')
                ->with('error', 'Impossible de supprimer ce tarif car il est utilisé dans des colis.');
        }

        $tarif->delete();

        return redirect()->route('tarifs.index')
            ->with('success', 'Tarif supprimé avec succès.');
    }
}

