<?php

namespace App\Http\Controllers;

use App\Models\Agence;
use App\Models\Caisse;
use App\Models\Client;
use App\Models\Coli;
use App\Models\ColisHistorique;
use App\Models\Devise;
use App\Models\EntrepriseTransporteur;
use App\Models\Paiement;
use App\Models\Tarif;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ColiController extends Controller
{
    public function index(Request $request)
    {
        $query = Coli::with(['client', 'agenceDepart', 'agenceArrivee', 'transporteur', 'devise', 'tarif', 'paiements']);

        // Filtrage par agence pour responsable d'agence
        $user = auth()->user();
        if ($user->isResponsableAgence() && $user->agence_id) {
            $query->pourAgence($user->agence_id);
        }

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
        $user = auth()->user();
        $clients = Client::where('statut', 'actif')->orderBy('nom')->get();

        // Filtrer les agences pour responsable d'agence
        $agences = Agence::orderBy('nom_agence')->get();
        if ($user->isResponsableAgence() && $user->agence_id) {
            $agences = Agence::where('id', $user->agence_id)->get();
        }

        $transporteurs = EntrepriseTransporteur::where('statut', 'actif')->orderBy('nom_entreprise')->get();
        $devises = Devise::where('actif', true)->orderBy('est_principale', 'desc')->orderBy('nom')->get();
        $tarifs = Tarif::where('actif', true)->orderBy('nom_tarif')->get();

        // Filtrer les caisses pour responsable d'agence
        $caisses = Caisse::where('statut', 'ouverte')->orderBy('nom_caisse')->get();
        if ($user->isResponsableAgence() && $user->agence_id) {
            $caisses = Caisse::where('statut', 'ouverte')
                ->where('agence_id', $user->agence_id)
                ->orderBy('nom_caisse')
                ->get();
        }

        return view('colis.create', compact('clients', 'agences', 'transporteurs', 'devises', 'tarifs', 'caisses'));
    }

    public function store(Request $request)
    {
        // Nettoyer frais_transport avant validation (enlever espaces, virgules et autres caractères non numériques sauf le point)
        if ($request->has('frais_transport')) {
            $fraisTransport = $request->frais_transport;
            // Enlever tous les espaces, virgules et autres caractères non numériques sauf le point
            $fraisTransport = preg_replace('/[^\d.]/', '', $fraisTransport);
            // Convertir en float puis en entier si c'est un nombre entier
            $fraisTransportNumeric = floatval($fraisTransport);
            $request->merge(['frais_transport' => $fraisTransportNumeric]);
        }

        // Préparer montant_paye si paiement_complet est coché
        $paiementComplet = $request->has('paiement_complet') && $request->paiement_complet;
        if ($paiementComplet && $request->has('frais_transport')) {
            $request->merge(['montant_paye' => $request->frais_transport]);
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'numero_suivi' => 'required|string|unique:colis,numero_suivi',
            'poids' => 'required|numeric|min:0',
            'dimensions' => 'nullable|string',
            'description_contenu' => 'nullable|string',
            'valeur_declaree' => 'nullable|numeric|min:0',
            'statut' => 'required|in:emballe,expedie_port,arrive_aeroport_depart,en_vol,arrive_aeroport_transit,arrive_aeroport_destination,en_dedouanement,arrive_entrepot,livre,retourne',
            'date_envoi' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_envoi',
            'agence_depart_id' => 'required|exists:agences,id',
            'agence_arrivee_id' => 'required|exists:agences,id',
            'pays_origine' => 'nullable|string|max:255',
            'ville_origine' => 'nullable|string|max:255',
            'transporteur_id' => 'nullable|exists:entreprises_transporteurs,id',
            'devise_id' => 'nullable|exists:devises,id',
            'tarif_id' => 'nullable|exists:tarifs,id',
            'frais_transport' => 'required|numeric|min:0',
            'frais_calcule' => 'nullable|numeric|min:0',
            'paiement_complet' => 'boolean',
            'paiement_partiel' => 'boolean',
            'caisse_id' => 'nullable|required_with:paiement_complet,paiement_partiel|exists:caisses,id',
            'montant_paye' => 'nullable|required_with:paiement_complet,paiement_partiel|numeric|min:0.01',
            'mode_paiement' => 'nullable|in:espece,carte,virement,cheque,mobile_money',
            'description_etape' => 'nullable|string|max:1000',
            'localisation_etape' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        // Retirer les champs de paiement de validated avant de créer le colis
        $paiementComplet = $request->has('paiement_complet') && $request->paiement_complet;
        $paiementPartiel = $request->has('paiement_partiel') && $request->paiement_partiel;
        $caisseId = $request->input('caisse_id');
        $montantPaye = $request->input('montant_paye');
        $modePaiement = $request->input('mode_paiement', 'espece');
        $descriptionEtape = $request->input('description_etape');
        $localisationEtape = $request->input('localisation_etape');
        $images = $request->file('images', []);

        unset(
            $validated['paiement_complet'],
            $validated['paiement_partiel'],
            $validated['caisse_id'],
            $validated['montant_paye'],
            $validated['mode_paiement'],
            $validated['description_etape'],
            $validated['localisation_etape'],
            $validated['images']
        );

        $coli = Coli::create($validated);

        // Enregistrer l'historique de création
        ColisHistorique::create([
            'coli_id' => $coli->id,
            'statut_avant' => null,
            'statut_apres' => $coli->statut,
            'user_id' => auth()->id(),
            'commentaire' => $descriptionEtape ?: 'Colis créé',
            'localisation' => $localisationEtape ?: ($coli->agenceDepart->nom_agence ?? null),
        ]);

        // Enregistrer les images si fournies
        if (!empty($images)) {
            foreach ($images as $file) {
                $path = $file->store('colis/' . $coli->id, 'public');

                $coli->images()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        // Calculer automatiquement le prix si un tarif est sélectionné
        if ($coli->tarif && $coli->poids) {
            $prixCalcule = $coli->tarif->calculerPrix(
                $coli->poids,
                $coli->agence_depart_id,
                $coli->agence_arrivee_id,
                $coli->transporteur_id
            );

            if ($prixCalcule) {
                $coli->update(['frais_calcule' => $prixCalcule]);
                // Si aucun prix manuel n'a été saisi, utiliser le prix calculé
                if (!$request->has('frais_transport') || $request->frais_transport == 0) {
                    $coli->update(['frais_transport' => $prixCalcule]);
                }
            }
        }

        // Gérer le paiement si demandé
        if (($paiementComplet || $paiementPartiel) && $caisseId) {
            $caisse = Caisse::findOrFail($caisseId);

            // Vérifier que la caisse est ouverte
            if (!$caisse->isOuverte()) {
                return back()->withInput()
                    ->with('error', 'Impossible d\'enregistrer le paiement sur une caisse fermée.');
            }

            // Déterminer le montant à payer
            if ($paiementComplet) {
                $montantPaye = $coli->frais_transport;
            } else {
                // Vérifier que le montant ne dépasse pas le total
                if ($montantPaye > $coli->frais_transport) {
                    return back()->withInput()
                        ->with('error', 'Le montant payé ne peut pas dépasser le montant total du colis.');
                }
            }

            // Conversion automatique si la devise du colis diffère de celle de la caisse
            $deviseColis = $coli->devise;
            $deviseCaisse = $caisse->devise;
            $montantEnregistre = $montantPaye;
            $deviseEnregistree = $deviseColis;

            if ($deviseCaisse && $deviseColis && $deviseCaisse->id !== $deviseColis->id) {
                // Convertir le montant vers la devise de la caisse
                $montantEnregistre = $deviseColis->convertirVers($deviseCaisse, $montantPaye);
                $deviseEnregistree = $deviseCaisse;
            }

            // Créer la transaction d'entrée (enregistrer dans la devise de la caisse)
            $transaction = Transaction::create([
                'caisse_id' => $caisseId,
                'type' => 'entree',
                'libelle' => 'Paiement colis ' . $coli->numero_suivi . ($paiementPartiel ? ' (Acompte)' : ''),
                'montant' => $montantEnregistre,
                'devise_id' => $deviseEnregistree->id,
                'coli_id' => $coli->id,
                'client_id' => $coli->client_id,
                'user_id' => auth()->id(),
                'date_transaction' => now(),
                'description' => ($paiementPartiel ? 'Acompte de ' : 'Paiement de ') .
                    number_format($montantPaye, 0, ',', ' ') . ' ' . $deviseColis->symbole .
                    ($deviseCaisse && $deviseCaisse->id !== $deviseColis->id ?
                        ' (converti: ' . number_format($montantEnregistre, 0, ',', ' ') . ' ' . $deviseCaisse->symbole . ')' : '') .
                    ' pour le colis ' . $coli->numero_suivi . ' - Client: ' . $coli->client->full_name,
            ]);

            // Créer le paiement (garder la devise originale du colis)
            $paiement = Paiement::create([
                'coli_id' => $coli->id,
                'caisse_id' => $caisseId,
                'transaction_id' => $transaction->id,
                'montant' => $montantPaye, // Montant original dans la devise du colis
                'devise_id' => $deviseColis->id,
                'mode_paiement' => $modePaiement,
                'date_paiement' => now(),
                'user_id' => auth()->id(),
                'notes' => ($paiementPartiel ? 'Acompte' : 'Paiement complet') .
                    ($deviseCaisse && $deviseCaisse->id !== $deviseColis->id ?
                        ' (converti en ' . $deviseCaisse->symbole . ')' : ''),
            ]);

            // Mettre à jour le solde de la caisse
            $caisse->mettreAJourSolde();

            // Mettre à jour le statut paye du colis (true si totalement payé)
            if ($coli->estTotalementPaye()) {
                $coli->update(['paye' => true]);
            }
        }

        return redirect()->route('colis.index')
            ->with('success', 'Colis créé avec succès.');
    }

    public function show(Coli $coli)
    {
        $coli->load([
            'client',
            'agenceDepart',
            'agenceArrivee',
            'transporteur',
            'devise',
            'tarif',
            'paiements.caisse',
            'paiements.user',
            'historique.user',
            'images',
        ]);
        return view('colis.show', compact('coli'));
    }

    public function edit(Coli $coli)
    {
        $user = auth()->user();
        $coli->load(['client', 'agenceDepart', 'agenceArrivee', 'transporteur', 'devise', 'tarif', 'paiements', 'images']);
        $clients = Client::where('statut', 'actif')->orderBy('nom')->get();

        // Filtrer les agences pour responsable d'agence
        $agences = Agence::orderBy('nom_agence')->get();
        if ($user->isResponsableAgence() && $user->agence_id) {
            $agences = Agence::where('id', $user->agence_id)->get();
        }

        $transporteurs = EntrepriseTransporteur::where('statut', 'actif')->orderBy('nom_entreprise')->get();
        $devises = Devise::where('actif', true)->orderBy('est_principale', 'desc')->orderBy('nom')->get();
        $tarifs = Tarif::where('actif', true)->orderBy('nom_tarif')->get();

        // Filtrer les caisses pour responsable d'agence
        $caisses = Caisse::where('statut', 'ouverte')->orderBy('nom_caisse')->get();
        if ($user->isResponsableAgence() && $user->agence_id) {
            $caisses = Caisse::where('statut', 'ouverte')
                ->where('agence_id', $user->agence_id)
                ->orderBy('nom_caisse')
                ->get();
        }

        return view('colis.edit', compact('coli', 'clients', 'agences', 'transporteurs', 'devises', 'tarifs', 'caisses'));
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
            'statut' => 'required|in:emballe,expedie_port,arrive_aeroport_depart,en_vol,arrive_aeroport_transit,arrive_aeroport_destination,en_dedouanement,arrive_entrepot,livre,retourne',
            'date_envoi' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_envoi',
            'agence_depart_id' => 'required|exists:agences,id',
            'agence_arrivee_id' => 'required|exists:agences,id',
            'pays_origine' => 'nullable|string|max:255',
            'ville_origine' => 'nullable|string|max:255',
            'transporteur_id' => 'nullable|exists:entreprises_transporteurs,id',
            'devise_id' => 'nullable|exists:devises,id',
            'tarif_id' => 'nullable|exists:tarifs,id',
            'frais_transport' => 'required|numeric|min:0',
            'frais_calcule' => 'nullable|numeric|min:0',
            'caisse_id' => 'nullable|exists:caisses,id',
            'montant_paye' => 'nullable|numeric|min:0.01',
            'mode_paiement' => 'nullable|in:espece,carte,virement,cheque,mobile_money',
            'description_etape' => 'nullable|string|max:1000',
            'localisation_etape' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        // Enregistrer l'historique si le statut change
        $statutAvant = $coli->statut;
        $descriptionEtape = $request->input('description_etape');
        $localisationEtape = $request->input('localisation_etape');
        $images = $request->file('images', []);

        // Retirer les champs d'historique de validated avant de mettre à jour
        unset($validated['description_etape'], $validated['localisation_etape'], $validated['images']);

        $coli->update($validated);

        // Ajouter de nouvelles images si fournies
        if (!empty($images)) {
            foreach ($images as $file) {
                $path = $file->store('colis/' . $coli->id, 'public');

                $coli->images()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        // Si le statut a changé, enregistrer dans l'historique
        if ($statutAvant !== $coli->statut) {
            ColisHistorique::create([
                'coli_id' => $coli->id,
                'statut_avant' => $statutAvant,
                'statut_apres' => $coli->statut,
                'user_id' => auth()->id(),
                'commentaire' => $descriptionEtape ?: 'Statut modifié',
                'localisation' => $localisationEtape ?: ($coli->agenceArrivee->nom_agence ?? $coli->agenceDepart->nom_agence ?? null),
            ]);
        }

        // Recalculer le prix si un tarif est sélectionné
        if ($coli->tarif && $coli->poids) {
            $prixCalcule = $coli->tarif->calculerPrix(
                $coli->poids,
                $coli->agence_depart_id,
                $coli->agence_arrivee_id,
                $coli->transporteur_id
            );

            if ($prixCalcule) {
                $coli->update(['frais_calcule' => $prixCalcule]);
            }
        }

        // Gérer l'ajout d'un acompte supplémentaire
        if ($request->has('caisse_id') && $request->has('montant_paye') && $request->montant_paye > 0) {
            $caisse = Caisse::findOrFail($request->caisse_id);

            // Vérifier que la caisse est ouverte
            if (!$caisse->isOuverte()) {
                return back()->withInput()
                    ->with('error', 'Impossible d\'enregistrer le paiement sur une caisse fermée.');
            }

            // Vérifier que le montant ne dépasse pas le reste à payer
            $montantRestant = $coli->montant_restant;
            if ($request->montant_paye > $montantRestant) {
                return back()->withInput()
                    ->with('error', 'Le montant payé ne peut pas dépasser le montant restant à payer (' . number_format($montantRestant, 0, ',', ' ') . ' FCFA).');
            }

            // Créer la transaction d'entrée
            $transaction = Transaction::create([
                'caisse_id' => $request->caisse_id,
                'type' => 'entree',
                'libelle' => 'Acompte colis ' . $coli->numero_suivi,
                'montant' => $request->montant_paye,
                'devise_id' => $coli->devise_id,
                'coli_id' => $coli->id,
                'client_id' => $coli->client_id,
                'user_id' => auth()->id(),
                'date_transaction' => now(),
                'description' => 'Acompte de ' . number_format($request->montant_paye, 0, ',', ' ') . ' FCFA pour le colis ' . $coli->numero_suivi . ' - Client: ' . $coli->client->full_name,
            ]);

            // Créer le paiement
            $paiement = Paiement::create([
                'coli_id' => $coli->id,
                'caisse_id' => $request->caisse_id,
                'transaction_id' => $transaction->id,
                'montant' => $request->montant_paye,
                'devise_id' => $coli->devise_id,
                'mode_paiement' => $request->input('mode_paiement', 'espece'),
                'date_paiement' => now(),
                'user_id' => auth()->id(),
                'notes' => 'Acompte supplémentaire',
            ]);

            // Mettre à jour le solde de la caisse
            $caisse->mettreAJourSolde();

            // Mettre à jour le statut paye du colis
            $coli->refresh();
            if ($coli->estTotalementPaye()) {
                $coli->update(['paye' => true]);
            }

            return redirect()->route('colis.edit', $coli)
                ->with('success', 'Acompte enregistré avec succès. Reste à payer: ' . number_format($coli->montant_restant, 0, ',', ' ') . ' FCFA');
        }

        return redirect()->route('colis.index')
            ->with('success', 'Colis mis à jour avec succès.');
    }

    public function destroy(Coli $coli)
    {
        $coli->delete();
        return redirect()->route('colis.index')
            ->with('success', 'Colis supprimé avec succès.');
    }

    /**
     * Ajouter une étape de suivi manuellement
     */
    public function addStep(Request $request, Coli $coli)
    {
        $validated = $request->validate([
            'statut_etape' => 'required|in:emballe,expedie_port,arrive_aeroport_depart,en_vol,arrive_aeroport_transit,arrive_aeroport_destination,en_dedouanement,arrive_entrepot,livre,retourne',
            'description_etape' => 'required|string|max:1000',
            'localisation_etape' => 'nullable|string|max:255',
        ]);

        $statutAvant = $coli->statut;
        $statutApres = $validated['statut_etape'];

        // Mettre à jour le statut du colis si différent
        if ($statutAvant !== $statutApres) {
            $coli->update(['statut' => $statutApres]);
        }

        // Créer l'entrée dans l'historique
        ColisHistorique::create([
            'coli_id' => $coli->id,
            'statut_avant' => $statutAvant,
            'statut_apres' => $statutApres,
            'user_id' => auth()->id(),
            'commentaire' => $validated['description_etape'],
            'localisation' => $validated['localisation_etape'] ?? null,
        ]);

        return redirect()->route('colis.show', $coli)
            ->with('success', 'Étape de suivi ajoutée avec succès.');
    }
}
