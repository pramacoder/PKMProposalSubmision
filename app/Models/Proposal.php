<?php

namespace App\Models;

use App\Enums\InternalResult;
use App\Enums\ProposalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Proposal extends Model
{
    protected $fillable = [
        'cycle_id', 'scheme_id', 'title', 'leader_id', 'supervisor_id', 'university_lecturer_id', 'theme_id',
        'status', 'similarity_percent', 'start_date', 'end_date', 'submitted_at',
        'admin_cost_amount', 'internal_result', 'belmawa_result', 'pimnas_status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProposalStatus::class,
            'internal_result' => InternalResult::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'submitted_at' => 'datetime',
            'similarity_percent' => 'decimal:2',
            'admin_cost_amount' => 'integer',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(Cycle::class);
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(Scheme::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function universityLecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'university_lecturer_id');
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProposalMember::class);
    }

    public function fundings(): HasMany
    {
        return $this->hasMany(ProposalFunding::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProposalFile::class)->where('is_current', true);
    }

    public function allFiles(): HasMany
    {
        return $this->hasMany(ProposalFile::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class)->orderBy('created_at');
    }

    public function supervisorValidations(): HasMany
    {
        return $this->hasMany(SupervisorValidation::class);
    }

    public function universityValidations(): HasMany
    {
        return $this->hasMany(UniversityValidation::class);
    }

    public function reviewerAssignments(): HasMany
    {
        return $this->hasMany(ReviewerAssignment::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(Revision::class);
    }

    public function currentRevision(): HasOne
    {
        return $this->hasOne(Revision::class)->latestOfMany('round');
    }

    public function semifinalResult(): HasOne
    {
        return $this->hasOne(SemifinalResult::class);
    }

    public function reviewSummaries(): HasMany
    {
        return $this->hasMany(ReviewSummary::class);
    }

    public function batchDecisions(): HasMany
    {
        return $this->hasMany(BatchDecision::class);
    }

    // ─── Business helpers ───────────────────────────────────────────────────────

    public function isEditable(): bool
    {
        return in_array($this->status, [ProposalStatus::Draft, ProposalStatus::Revision, ProposalStatus::FinalUpload]);
    }

    public function isFunded(): bool
    {
        return $this->scheme?->is_funded ?? false;
    }

    /** Total dana dari semua sumber (Rp). */
    public function totalFunding(): int
    {
        return $this->fundings->sum('amount');
    }

    /** Persentase komponen administrasi dari total dana. */
    public function adminCostPercent(): float
    {
        $total = $this->totalFunding();

        return $total > 0 ? round(($this->admin_cost_amount / $total) * 100, 2) : 0;
    }
}
