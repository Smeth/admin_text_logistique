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
        'paye',
    ];

    protected $casts = [
        'date_envoi' => 'date',
        'date_livraison_prevue' => 'date',
        'poids' => 'decimal:2',
        'valeur_declaree' => 'decimal:2',
        'frais_transport' => 'decimal:2',
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

    public function isLivre(): bool
    {
        return $this->statut === 'livre';
    }
}
