<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agence extends Model
{
    protected $fillable = [
        'nom_agence',
        'localisation',
        'code_agence',
    ];

    public function colisDepart(): HasMany
    {
        return $this->hasMany(Coli::class, 'agence_depart_id');
    }

    public function colisArrivee(): HasMany
    {
        return $this->hasMany(Coli::class, 'agence_arrivee_id');
    }
}
