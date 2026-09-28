<?php

namespace App\Models;

use App\Enums\ChecklistGroup;
use App\Enums\SchemeCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scheme extends Model
{
    protected $fillable = ['code', 'name', 'is_funded', 'checklist_group'];

    protected function casts(): array
    {
        return [
            'is_funded' => 'boolean',
            'code' => SchemeCode::class,
            'checklist_group' => ChecklistGroup::class,
        ];
    }

    public function schemeSettings(): HasMany
    {
        return $this->hasMany(CycleSchemeSetting::class);
    }

    public function documentRequirements(): HasMany
    {
        return $this->hasMany(DocumentRequirement::class)->orderBy('sort');
    }

    public function rubrics(): HasMany
    {
        return $this->hasMany(Rubric::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function settingForCycle(int $cycleId): ?CycleSchemeSetting
    {
        return $this->schemeSettings()->where('cycle_id', $cycleId)->first();
    }
}
