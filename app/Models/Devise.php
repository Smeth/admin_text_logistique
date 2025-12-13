<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Devise extends Model
{
    protected $fillable = [
        'code',
        'nom',
        'symbole',
        'taux_change',
        'est_principale',
        'actif',
    ];

    protected $casts = [
        'taux_change' => 'decimal:4',
        'est_principale' => 'boolean',
        'actif' => 'boolean',
    ];

    public function colis(): HasMany
    {
        return $this->hasMany(Coli::class);
    }

    /**
     * Obtenir la devise principale
     */
    public static function principale()
    {
        return static::where('est_principale', true)->first();
    }

    /**
     * Convertir un montant vers cette devise
     */
    public function convertir($montant, Devise $fromDevise)
    {
        if ($fromDevise->id === $this->id) {
            return $montant;
        }

        // Convertir vers la devise principale d'abord
        $montantPrincipal = $montant / $fromDevise->taux_change;
        
        // Puis vers la devise cible
        return $montantPrincipal * $this->taux_change;
    }

    /**
     * Formater un montant avec le symbole
     */
    public function formater($montant)
    {
        return number_format($montant, 0, ',', ' ') . ' ' . $this->symbole;
    }
}

