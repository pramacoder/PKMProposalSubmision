<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhaseWindow extends Model
{
    protected $fillable = [
        'cycle_id', 'phase', 'opens_at', 'closes_at',
        'forced_open', 'forced_closed', 'forced_reason', 'forced_by',
    ];

    protected function casts(): array
    {
        return [
            'opens_at' => 'datetime',
            'closes_at' => 'datetime',
            'forced_open' => 'boolean',
            'forced_closed' => 'boolean',
        ];
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(Cycle::class);
    }

    public function forcedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'forced_by');
    }

    /** Apakah jendela ini saat ini terbuka untuk aksi tulis? */
    public function isOpen(): bool
    {
        if ($this->forced_closed) {
            return false;
        }

        if ($this->forced_open) {
            return true;
        }

        $now = now();

        return ($this->opens_at === null || $now->gte($this->opens_at))
            && ($this->closes_at === null || $now->lte($this->closes_at));
    }
}
