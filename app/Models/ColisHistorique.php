<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ColisHistorique extends Model
{
    protected $table = 'colis_historique';

    protected $fillable = [
        'coli_id',
        'statut_avant',
        'statut_apres',
        'user_id',
        'commentaire',
        'localisation',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function coli(): BelongsTo
    {
        return $this->belongsTo(Coli::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
