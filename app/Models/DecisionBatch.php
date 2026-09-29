<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DecisionBatch extends Model
{
    protected $fillable = [
        'cycle_id',
        'scheme_id',
        'name',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
        ];
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(Cycle::class);
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(Scheme::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(BatchParticipant::class, 'batch_id');
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(BatchDecision::class, 'batch_id');
    }

    public function isDecided(): bool
    {
        return !is_null($this->decided_at);
    }
}
