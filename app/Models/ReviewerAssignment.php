<?php

namespace App\Models;

use App\Enums\ReviewStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewerAssignment extends Model
{
    protected $fillable = [
        'proposal_id', 'reviewer_id', 'stage', 'form_id', 'rubric_id',
        'status', 'due_at', 'assigned_by',
    ];

    protected function casts(): array
    {
        return [
            'stage' => ReviewStage::class,
            'due_at' => 'datetime',
        ];
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function adminResults(): HasMany
    {
        return $this->hasMany(AdminReviewResult::class, 'assignment_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(SubstantiveScore::class, 'assignment_id');
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }
}
