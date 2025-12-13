<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
