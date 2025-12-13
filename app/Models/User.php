<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'agence_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role?->name === 'agent';
    }

    public function isResponsableAgence(): bool
    {
        return $this->role?->name === 'responsable_agence';
    }

    public function isSuperviseur(): bool
    {
        return $this->role?->name === 'superviseur';
    }

    /**
     * Vérifier si l'utilisateur peut accéder à une agence
     */
    public function peutAccederAgence(?int $agenceId): bool
    {
        if ($this->isAdmin()) {
            return true; // Admin accède à tout
        }
        
        if ($this->isSuperviseur()) {
            return true; // Superviseur voit tout
        }
        
        if ($this->isResponsableAgence()) {
            return $this->agence_id === $agenceId;
        }
        
        return false;
    }
}
