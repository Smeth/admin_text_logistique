<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'type',
        'statut',
        'notes',
    ];

    public function colis(): HasMany
    {
        return $this->hasMany(Coli::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->nom . ' ' . $this->prenom);
    }

    /**
     * Scope pour filtrer les clients ayant des colis liés à une agence
     */
    public function scopeAvecColisAgence($query, $agenceId)
    {
        return $query->whereHas('colis', function($q) use ($agenceId) {
            $q->where('agence_depart_id', $agenceId)
              ->orWhere('agence_arrivee_id', $agenceId);
        });
    }
}
