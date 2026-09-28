<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cycle extends Model
{
    protected $fillable = ['year', 'name', 'is_active', 'max_proposals_per_supervisor'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'year' => 'integer',
            'max_proposals_per_supervisor' => 'integer',
        ];
    }

    public function schemeSettings(): HasMany
    {
        return $this->hasMany(CycleSchemeSetting::class);
    }

    public function themes(): HasMany
    {
        return $this->hasMany(Theme::class)->orderBy('sort');
    }

    public function phaseWindows(): HasMany
    {
        return $this->hasMany(PhaseWindow::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function checklistForms(): HasMany
    {
        return $this->hasMany(ChecklistForm::class);
    }

    public function rubrics(): HasMany
    {
        return $this->hasMany(Rubric::class);
    }

    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }
}
