<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchDecision extends Model
{
    protected $fillable = [
        'batch_id',
        'proposal_id',
        'decision',
        'note',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(DecisionBatch::class, 'batch_id');
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
