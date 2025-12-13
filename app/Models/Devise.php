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
     * Si aucune devise principale n'existe, retourne la première devise active
     */
    public static function principale()
    {
        $principale = static::where('est_principale', true)->first();
        
        if (!$principale) {
            // Si aucune devise principale, prendre la première devise active
            $principale = static::where('actif', true)->orderBy('created_at')->first();
            
            // Si une devise est trouvée, la marquer comme principale
            if ($principale) {
                $principale->update(['est_principale' => true, 'taux_change' => 1.0000]);
            }
        }
        
        return $principale;
    }

    /**
     * Convertir un montant vers cette devise
     */
    public function convertir($montant, ?Devise $fromDevise = null)
    {
        // Si pas de devise source, utiliser la devise principale
        if (!$fromDevise) {
            $fromDevise = static::principale();
        }
        
        if (!$fromDevise || $fromDevise->id === $this->id) {
            return $montant;
        }

        // Convertir vers la devise principale d'abord
        $montantPrincipal = $montant / $fromDevise->taux_change;
        
        // Puis vers la devise cible
        return $montantPrincipal * $this->taux_change;
    }

    /**
     * Convertir un montant depuis cette devise vers une autre
     */
    public function convertirVers(Devise $toDevise, $montant)
    {
        return $toDevise->convertir($montant, $this);
    }

    /**
     * Formater un montant avec le symbole
     */
    public function formater($montant)
    {
        return number_format($montant, 0, ',', ' ') . ' ' . $this->symbole;
    }
}

