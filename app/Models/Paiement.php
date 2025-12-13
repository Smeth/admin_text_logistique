<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    protected $fillable = [
        'coli_id',
        'caisse_id',
        'transaction_id',
        'montant',
        'devise_id',
        'mode_paiement',
        'date_paiement',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    public function coli(): BelongsTo
    {
        return $this->belongsTo(Coli::class);
    }

    public function caisse(): BelongsTo
    {
        return $this->belongsTo(Caisse::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function devise(): BelongsTo
    {
        return $this->belongsTo(Devise::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

