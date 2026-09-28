<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nim',
        'nidn',
        'nuptk',
        'study_program',
        'cohort_year',
        'notification_email',
        'notification_email_verified_at',
        'must_change_password',
        'claimed_at',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'notification_email_verified_at' => 'datetime',
            'claimed_at' => 'datetime',
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function roleRecords(): HasMany
    {
        return $this->hasMany(RoleUser::class);
    }

    public function ledProposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'leader_id');
    }

    public function supervisedProposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'supervisor_id');
    }

    public function reviewerAssignments(): HasMany
    {
        return $this->hasMany(ReviewerAssignment::class, 'reviewer_id');
    }

    public function accountClaims(): HasMany
    {
        return $this->hasMany(AccountClaim::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_id');
    }

    // ─── Role helpers ──────────────────────────────────────────────────────────

    /** @return Role[] */
    public function roles(): array
    {
        return $this->roleRecords->map(
            fn ($r) => Role::from($r->role)
        )->all();
    }

    public function hasRole(Role $role): bool
    {
        return $this->roleRecords->contains('role', $role->value);
    }

    public function hasAnyRole(Role ...$roles): bool
    {
        $values = array_map(fn ($r) => $r->value, $roles);

        return $this->roleRecords->whereIn('role', $values)->isNotEmpty();
    }

    /** Peran tertinggi untuk redirect dashboard. */
    public function primaryRole(): Role
    {
        $priority = [
            Role::SuperOperator,
            Role::Operator,
            Role::UniversityLecturer,
            Role::Reviewer,
            Role::Supervisor,
            Role::Student,
        ];

        foreach ($priority as $role) {
            if ($this->hasRole($role)) {
                return $role;
            }
        }

        return Role::Student;
    }

    public function isClaimed(): bool
    {
        return $this->claimed_at !== null;
    }

    public function hasNuptk(): bool
    {
        return ! empty($this->nuptk);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }

    public function scopeUnclaimed($query): mixed
    {
        return $query->whereNull('claimed_at');
    }
}
