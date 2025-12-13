<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarif extends Model
{
    protected $fillable = [
        'nom_tarif',
        'description',
        'prix_par_kilo',
        'prix_minimum',
        'prix_maximum',
        'agence_depart_id',
        'agence_arrivee_id',
        'transporteur_id',
        'date_debut',
        'date_fin',
        'actif',
    ];

    protected $casts = [
        'prix_par_kilo' => 'decimal:2',
        'prix_minimum' => 'decimal:2',
        'prix_maximum' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'actif' => 'boolean',
    ];

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

    public function colis(): HasMany
    {
        return $this->hasMany(Coli::class);
    }

    /**
     * Calculer le prix basé sur le poids
     */
    public function calculerPrix($poids, $agenceDepartId = null, $agenceArriveeId = null, $transporteurId = null)
    {
        // Vérifier si le tarif est applicable
        if (!$this->estApplicable($agenceDepartId, $agenceArriveeId, $transporteurId)) {
            return null;
        }

        // Calculer le prix
        $prix = $poids * $this->prix_par_kilo;

        // Appliquer le prix minimum
        if ($prix < $this->prix_minimum) {
            $prix = $this->prix_minimum;
        }

        // Appliquer le prix maximum si défini
        if ($this->prix_maximum && $prix > $this->prix_maximum) {
            $prix = $this->prix_maximum;
        }

        return round($prix, 2);
    }

    /**
     * Vérifier si le tarif est applicable
     */
    public function estApplicable($agenceDepartId = null, $agenceArriveeId = null, $transporteurId = null)
    {
        // Vérifier si le tarif est actif
        if (!$this->actif) {
            return false;
        }

        // Vérifier les dates de validité
        $now = now();
        if ($this->date_debut && $now->lt($this->date_debut)) {
            return false;
        }
        if ($this->date_fin && $now->gt($this->date_fin)) {
            return false;
        }

        // Si le tarif est spécifique à une agence de départ
        if ($this->agence_depart_id && $agenceDepartId != $this->agence_depart_id) {
            return false;
        }

        // Si le tarif est spécifique à une agence d'arrivée
        if ($this->agence_arrivee_id && $agenceArriveeId != $this->agence_arrivee_id) {
            return false;
        }

        // Si le tarif est spécifique à un transporteur
        if ($this->transporteur_id && $transporteurId != $this->transporteur_id) {
            return false;
        }

        return true;
    }

    /**
     * Obtenir le tarif applicable pour une configuration donnée
     */
    public static function trouverApplicable($agenceDepartId = null, $agenceArriveeId = null, $transporteurId = null)
    {
        return static::where('actif', true)
            ->where(function($query) use ($agenceDepartId) {
                $query->whereNull('agence_depart_id')
                      ->orWhere('agence_depart_id', $agenceDepartId);
            })
            ->where(function($query) use ($agenceArriveeId) {
                $query->whereNull('agence_arrivee_id')
                      ->orWhere('agence_arrivee_id', $agenceArriveeId);
            })
            ->where(function($query) use ($transporteurId) {
                $query->whereNull('transporteur_id')
                      ->orWhere('transporteur_id', $transporteurId);
            })
            ->where(function($query) {
                $query->whereNull('date_debut')
                      ->orWhere('date_debut', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->orderBy('prix_par_kilo', 'asc') // Prendre le tarif le moins cher applicable
            ->first();
    }
}

