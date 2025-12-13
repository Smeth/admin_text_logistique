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
            'code' => 'required|string|max:10|unique:devises,code',
            'nom' => 'required|string|max:255',
            'symbole' => 'required|string|max:10',
            'taux_change' => 'required|numeric|min:0',
            'est_principale' => 'boolean',
            'actif' => 'boolean',
        ]);

        // Vérifier s'il existe déjà une devise principale
        $devisePrincipaleExistante = Devise::where('est_principale', true)->first();

        // Si cette devise est marquée comme principale, retirer le statut des autres
        if ($request->boolean('est_principale')) {
            Devise::where('est_principale', true)->update(['est_principale' => false]);
        } elseif (!$devisePrincipaleExistante) {
            // S'il n'y a pas de devise principale et que celle-ci n'est pas marquée comme principale,
            // la marquer automatiquement comme principale (première devise créée)
            $validated['est_principale'] = true;
        }

        // Si c'est la première devise créée et qu'elle est principale, s'assurer que le taux est 1.0000
        if ($validated['est_principale'] && !$devisePrincipaleExistante) {
            $validated['taux_change'] = 1.0000;
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
            'code' => 'required|string|max:10|unique:devises,code,' . $devise->id,
            'nom' => 'required|string|max:255',
            'symbole' => 'required|string|max:10',
            'taux_change' => 'required|numeric|min:0',
            'est_principale' => 'boolean',
            'actif' => 'boolean',
        ]);

        $estPrincipaleAvant = $devise->est_principale;
        $estPrincipaleApres = $request->boolean('est_principale');

        // Si cette devise est marquée comme principale, retirer le statut des autres
        if ($estPrincipaleApres) {
            Devise::where('id', '!=', $devise->id)
                  ->where('est_principale', true)
                  ->update(['est_principale' => false]);
        } elseif ($estPrincipaleAvant && !$estPrincipaleApres) {
            // Si on retire le statut principal de cette devise, vérifier qu'il reste une devise principale
            $autreDevisePrincipale = Devise::where('id', '!=', $devise->id)
                ->where('est_principale', true)
                ->first();
            
            if (!$autreDevisePrincipale) {
                // S'il n'y a plus de devise principale, prendre la première devise active
                $premiereDevise = Devise::where('id', '!=', $devise->id)
                    ->where('actif', true)
                    ->orderBy('created_at')
                    ->first();
                
                if ($premiereDevise) {
                    $premiereDevise->update(['est_principale' => true, 'taux_change' => 1.0000]);
                } else {
                    // Si c'est la seule devise, la garder comme principale
                    return redirect()->route('devises.index')
                        ->with('error', 'Impossible de retirer le statut principal : c\'est la seule devise disponible.');
                }
            }
        }

        // Si la devise devient principale, s'assurer que son taux est 1.0000
        if ($estPrincipaleApres && !$estPrincipaleAvant) {
            $validated['taux_change'] = 1.0000;
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

