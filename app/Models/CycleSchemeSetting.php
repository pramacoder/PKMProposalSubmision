<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CycleSchemeSetting extends Model
{
    protected $fillable = [
        'cycle_id', 'scheme_id', 'quota',
        'min_pt', 'max_pt', 'max_partner',
        'min_belmawa', 'max_belmawa',
        'max_admin_percent', 'min_months', 'max_months',
    ];

    protected function casts(): array
    {
        return [
            'quota' => 'integer',
            'min_pt' => 'integer',
            'max_pt' => 'integer',
            'max_partner' => 'integer',
            'min_belmawa' => 'integer',
            'max_belmawa' => 'integer',
            'max_admin_percent' => 'integer',
            'min_months' => 'integer',
            'max_months' => 'integer',
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

    /** Batas dana PT per proposal (wajib maks Rp2jt sesuai aturan, dikonfigurasi per siklus). */
    public function maxUniversityFunding(): int
    {
        return $this->max_pt;
    }

    /** Batas dana mitra (opsional, maks Rp1jt default). */
    public function maxPartnerFunding(): int
    {
        return $this->max_partner;
    }
}
