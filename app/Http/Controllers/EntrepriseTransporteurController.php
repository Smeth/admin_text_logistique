<?php

namespace App\Http\Controllers;

use App\Models\EntrepriseTransporteur;
use Illuminate\Http\Request;

class EntrepriseTransporteurController extends Controller
{
    public function index(Request $request)
    {
        $query = EntrepriseTransporteur::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom_entreprise', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }

        $transporteurs = $query->latest()->paginate(15);
        return view('transporteurs.index', compact('transporteurs'));
    }

    public function create()
    {
        return view('transporteurs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_entreprise' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'type_transport' => 'nullable|string|max:255',
            'statut' => 'required|in:actif,inactif',
        ]);

        EntrepriseTransporteur::create($validated);

        return redirect()->route('transporteurs.index')
            ->with('success', 'Entreprise transporteur créée avec succès.');
    }

    public function show(EntrepriseTransporteur $transporteur)
    {
        $transporteur->load('colis.client');
        return view('transporteurs.show', compact('transporteur'));
    }

    public function edit(EntrepriseTransporteur $transporteur)
    {
        return view('transporteurs.edit', compact('transporteur'));
    }

    public function update(Request $request, EntrepriseTransporteur $transporteur)
    {
        $validated = $request->validate([
            'nom_entreprise' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'type_transport' => 'nullable|string|max:255',
            'statut' => 'required|in:actif,inactif',
        ]);

        $transporteur->update($validated);

        return redirect()->route('transporteurs.index')
            ->with('success', 'Entreprise transporteur mise à jour avec succès.');
    }

    public function destroy(EntrepriseTransporteur $transporteur)
    {
        if ($transporteur->colis()->count() > 0) {
            return redirect()->route('transporteurs.index')
                ->with('error', 'Impossible de supprimer cette entreprise car elle a des colis associés.');
        }

        $transporteur->delete();

        return redirect()->route('transporteurs.index')
            ->with('success', 'Entreprise transporteur supprimée avec succès.');
    }
}
