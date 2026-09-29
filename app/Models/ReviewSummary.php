<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewSummary extends Model
{
    protected $fillable = [
        'proposal_id', 'stage', 'total', 'computed_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'computed_at' => 'datetime',
        ];
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
