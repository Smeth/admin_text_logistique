<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caisse extends Model
{
    protected $fillable = [
        'nom_caisse',
        'agence_id',
        'responsable_id',
        'solde_initial',
        'solde_actuel',
        'statut',
        'date_ouverture',
        'date_fermeture',
        'notes',
    ];

    protected $casts = [
        'solde_initial' => 'decimal:2',
        'solde_actuel' => 'decimal:2',
        'date_ouverture' => 'datetime',
        'date_fermeture' => 'datetime',
    ];

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    /**
     * Calculer le solde actuel basé sur les transactions
     */
    public function calculerSolde()
    {
        $entrees = $this->transactions()
            ->where('type', 'entree')
            ->sum('montant');
        
        $sorties = $this->transactions()
            ->where('type', 'sortie')
            ->sum('montant');
        
        return $this->solde_initial + $entrees - $sorties;
    }

    /**
     * Mettre à jour le solde actuel
     */
    public function mettreAJourSolde()
    {
        $this->solde_actuel = $this->calculerSolde();
        $this->save();
    }

    /**
     * Ouvrir la caisse
     */
    public function ouvrir()
    {
        $this->statut = 'ouverte';
        $this->date_ouverture = now();
        $this->date_fermeture = null;
        $this->save();
    }

    /**
     * Fermer la caisse
     */
    public function fermer()
    {
        $this->statut = 'fermee';
        $this->date_fermeture = now();
        $this->mettreAJourSolde();
        $this->save();
    }

    public function isOuverte(): bool
    {
        return $this->statut === 'ouverte';
    }
}

