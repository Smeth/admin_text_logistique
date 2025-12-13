<?php

namespace App\Http\Controllers;

use App\Models\Agence;
use Illuminate\Http\Request;

class AgenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Agence::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom_agence', 'like', "%{$search}%")
                  ->orWhere('localisation', 'like', "%{$search}%")
                  ->orWhere('code_agence', 'like', "%{$search}%");
            });
        }

        $agences = $query->latest()->paginate(15);
        return view('agences.index', compact('agences'));
    }

    public function create()
    {
        return view('agences.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_agence' => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'code_agence' => 'required|string|unique:agences,code_agence',
        ]);

        Agence::create($validated);

        return redirect()->route('agences.index')
            ->with('success', 'Agence créée avec succès.');
    }

    public function show(Agence $agence)
    {
        $agence->load(['colisDepart', 'colisArrivee']);
        return view('agences.show', compact('agence'));
    }

    public function edit(Agence $agence)
    {
        return view('agences.edit', compact('agence'));
    }

    public function update(Request $request, Agence $agence)
    {
        $validated = $request->validate([
            'nom_agence' => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'code_agence' => 'required|string|unique:agences,code_agence,' . $agence->id,
        ]);

        $agence->update($validated);

        return redirect()->route('agences.index')
            ->with('success', 'Agence mise à jour avec succès.');
    }

    public function destroy(Agence $agence)
    {
        if ($agence->colisDepart()->count() > 0 || $agence->colisArrivee()->count() > 0) {
            return redirect()->route('agences.index')
                ->with('error', 'Impossible de supprimer cette agence car elle a des colis associés.');
        }

        $agence->delete();

        return redirect()->route('agences.index')
            ->with('success', 'Agence supprimée avec succès.');
    }
}
