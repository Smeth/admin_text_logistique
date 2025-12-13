<?php

namespace App\Http\Controllers;

use App\Models\Devise;
use Illuminate\Http\Request;

class DeviseController extends Controller
{
    public function index(Request $request)
    {
        $query = Devise::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%")
                  ->orWhere('symbole', 'like', "%{$search}%");
            });
        }

        $devises = $query->orderBy('est_principale', 'desc')->orderBy('nom')->paginate(15);
        return view('devises.index', compact('devises'));
    }

    public function create()
    {
        return view('devises.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:3|unique:devises,code',
            'nom' => 'required|string|max:255',
            'symbole' => 'required|string|max:10',
            'taux_change' => 'required|numeric|min:0',
            'est_principale' => 'boolean',
            'actif' => 'boolean',
        ]);

        // Si cette devise est marquée comme principale, retirer le statut des autres
        if ($request->boolean('est_principale')) {
            Devise::where('est_principale', true)->update(['est_principale' => false]);
        }

        Devise::create($validated);

        return redirect()->route('devises.index')
            ->with('success', 'Devise créée avec succès.');
    }

    public function show(Devise $devise)
    {
        $devise->load('colis');
        return view('devises.show', compact('devise'));
    }

    public function edit(Devise $devise)
    {
        return view('devises.edit', compact('devise'));
    }

    public function update(Request $request, Devise $devise)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:3|unique:devises,code,' . $devise->id,
            'nom' => 'required|string|max:255',
            'symbole' => 'required|string|max:10',
            'taux_change' => 'required|numeric|min:0',
            'est_principale' => 'boolean',
            'actif' => 'boolean',
        ]);

        // Si cette devise est marquée comme principale, retirer le statut des autres
        if ($request->boolean('est_principale')) {
            Devise::where('id', '!=', $devise->id)
                  ->where('est_principale', true)
                  ->update(['est_principale' => false]);
        }

        $devise->update($validated);

        return redirect()->route('devises.index')
            ->with('success', 'Devise mise à jour avec succès.');
    }

    public function destroy(Devise $devise)
    {
        if ($devise->est_principale) {
            return redirect()->route('devises.index')
                ->with('error', 'Impossible de supprimer la devise principale.');
        }

        if ($devise->colis()->count() > 0) {
            return redirect()->route('devises.index')
                ->with('error', 'Impossible de supprimer cette devise car elle est utilisée dans des colis.');
        }

        $devise->delete();

        return redirect()->route('devises.index')
            ->with('success', 'Devise supprimée avec succès.');
    }
}

