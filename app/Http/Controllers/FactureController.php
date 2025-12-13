<?php

namespace App\Http\Controllers;

use App\Models\Coli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class FactureController extends Controller
{
    /**
     * Générer une facture pour un colis
     */
    public function facture(Coli $coli)
    {
        $coli->load(['client', 'agenceDepart', 'agenceArrivee', 'transporteur', 'devise', 'tarif', 'paiements']);
        
        return view('factures.facture', compact('coli'));
    }

    /**
     * Générer un reçu pour un colis
     */
    public function recu(Coli $coli)
    {
        $coli->load(['client', 'agenceDepart', 'agenceArrivee', 'transporteur', 'devise', 'paiements.caisse', 'paiements.user']);
        
        return view('factures.recu', compact('coli'));
    }

    /**
     * Télécharger une facture en PDF (version simple HTML pour l'instant)
     */
    public function downloadFacture(Coli $coli)
    {
        $coli->load(['client', 'agenceDepart', 'agenceArrivee', 'transporteur', 'devise', 'tarif', 'paiements']);
        
        $html = view('factures.facture', compact('coli'))->render();
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="facture-' . $coli->numero_suivi . '.html"');
    }

    /**
     * Télécharger un reçu en PDF (version simple HTML pour l'instant)
     */
    public function downloadRecu(Coli $coli)
    {
        $coli->load(['client', 'agenceDepart', 'agenceArrivee', 'transporteur', 'devise', 'paiements.caisse', 'paiements.user']);
        
        $html = view('factures.recu', compact('coli'))->render();
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="recu-' . $coli->numero_suivi . '.html"');
    }
}
