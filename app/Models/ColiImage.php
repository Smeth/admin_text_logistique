<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ColiImage extends Model
{
    protected $table = 'coli_images';

    protected $fillable = [
        'coli_id',
        'path',
        'original_name',
        'size',
    ];

    public function coli(): BelongsTo
    {
        return $this->belongsTo(Coli::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}


