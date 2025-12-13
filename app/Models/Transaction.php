<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'caisse_id',
        'type',
        'libelle',
        'montant',
        'devise_id',
        'coli_id',
        'client_id',
        'user_id',
        'date_transaction',
        'description',
        'reference',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_transaction' => 'date',
    ];

    public function caisse(): BelongsTo
    {
        return $this->belongsTo(Caisse::class);
    }

    public function devise(): BelongsTo
    {
        return $this->belongsTo(Devise::class);
    }

    public function coli(): BelongsTo
    {
        return $this->belongsTo(Coli::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paiement(): BelongsTo
    {
        return $this->belongsTo(Paiement::class, 'id', 'transaction_id');
    }

    public function isEntree(): bool
    {
        return $this->type === 'entree';
    }

    public function isSortie(): bool
    {
        return $this->type === 'sortie';
    }
}

