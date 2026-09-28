<?php

namespace App\Models;

use App\Enums\FormStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rubric extends Model
{
    protected $fillable = ['cycle_id', 'scheme_id', 'status', 'confirmed_by', 'confirmed_at'];

    protected function casts(): array
    {
        return [
            'status' => FormStatus::class,
            'confirmed_at' => 'datetime',
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

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function criteria(): HasMany
    {
        return $this->hasMany(RubricCriterion::class)->orderBy('sort');
    }

    public function isLocked(): bool
    {
        return $this->status === FormStatus::Confirmed;
    }

    /** Total bobot harus 100 sebelum dikonfirmasi. */
    public function totalWeight(): int
    {
        return $this->criteria->sum('weight');
    }
}
