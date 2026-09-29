<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupervisorValidation extends Model
{
    protected $fillable = [
        'proposal_id', 'round', 'supervisor_id', 'decision', 'note', 'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'round' => 'integer',
            'decided_at' => 'datetime',
        ];
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
