<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntrepriseTransporteur extends Model
{
    protected $table = 'entreprises_transporteurs';

    protected $fillable = [
        'nom_entreprise',
        'email',
        'telephone',
        'adresse',
        'type_transport',
        'statut',
    ];

    public function colis(): HasMany
    {
        return $this->hasMany(Coli::class, 'transporteur_id');
    }
}
