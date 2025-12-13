<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coli extends Model
{
    protected $fillable = [
        'client_id',
        'numero_suivi',
        'poids',
        'dimensions',
        'description_contenu',
        'valeur_declaree',
        'statut',
        'date_envoi',
        'date_livraison_prevue',
        'agence_depart_id',
        'agence_arrivee_id',
        'transporteur_id',
        'frais_transport',
        'devise_id',
        'tarif_id',
        'frais_calcule',
        'paye',
    ];

    protected $casts = [
        'date_envoi' => 'date',
        'date_livraison_prevue' => 'date',
        'poids' => 'decimal:2',
        'valeur_declaree' => 'decimal:2',
        'frais_transport' => 'decimal:2',
        'frais_calcule' => 'decimal:2',
        'paye' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function agenceDepart(): BelongsTo
    {
        return $this->belongsTo(Agence::class, 'agence_depart_id');
    }

    public function agenceArrivee(): BelongsTo
    {
        return $this->belongsTo(Agence::class, 'agence_arrivee_id');
    }

    public function transporteur(): BelongsTo
    {
        return $this->belongsTo(EntrepriseTransporteur::class, 'transporteur_id');
    }

    public function devise(): BelongsTo
    {
        return $this->belongsTo(Devise::class);
    }

    public function tarif(): BelongsTo
    {
        return $this->belongsTo(Tarif::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isLivre(): bool
    {
        return $this->statut === 'livre';
    }

    /**
     * Calculer automatiquement le prix si un tarif est sélectionné
     */
    public function calculerPrixAutomatique()
    {
        if ($this->tarif && $this->poids) {
            $this->frais_calcule = $this->tarif->calculerPrix(
                $this->poids,
                $this->agence_depart_id,
                $this->agence_arrivee_id,
                $this->transporteur_id
            );
            
            // Si un prix est calculé, l'utiliser comme frais_transport
            if ($this->frais_calcule) {
                $this->frais_transport = $this->frais_calcule;
            }
        }
    }

    /**
     * Calculer le montant total payé
     */
    public function getTotalPayeAttribute()
    {
        return $this->paiements()->sum('montant');
    }

    /**
     * Calculer le montant restant à payer
     */
    public function getMontantRestantAttribute()
    {
        return max(0, $this->frais_transport - $this->total_paye);
    }

    /**
     * Obtenir le statut de paiement
     */
    public function getStatutPaiementAttribute()
    {
        $totalPaye = $this->total_paye;
        
        if ($totalPaye == 0) {
            return 'non_paye';
        } elseif ($totalPaye >= $this->frais_transport) {
            return 'paye';
        } else {
            return 'partiel';
        }
    }

    /**
     * Vérifier si le colis est totalement payé
     */
    public function estTotalementPaye(): bool
    {
        return $this->total_paye >= $this->frais_transport;
    }

    /**
     * Vérifier si le colis est partiellement payé
     */
    public function estPartiellementPaye(): bool
    {
        $totalPaye = $this->total_paye;
        return $totalPaye > 0 && $totalPaye < $this->frais_transport;
    }
}
