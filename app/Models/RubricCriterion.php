<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RubricCriterion extends Model
{
    protected $fillable = ['rubric_id', 'group_label', 'label', 'weight', 'sort'];

    public function rubric(): BelongsTo
    {
        return $this->belongsTo(Rubric::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(SubstantiveScore::class, 'criterion_id');
    }

    /** Hitung nilai untuk skor tertentu: nilai = bobot × skor. */
    public function computeValue(int $score): int
    {
        return $this->weight * $score;
    }
}
