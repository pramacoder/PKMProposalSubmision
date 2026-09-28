<?php

namespace App\Models;

use App\Enums\ChecklistGroup;
use App\Enums\FormStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistForm extends Model
{
    protected $fillable = ['cycle_id', 'checklist_group', 'status', 'confirmed_by', 'confirmed_at'];

    protected function casts(): array
    {
        return [
            'status' => FormStatus::class,
            'checklist_group' => ChecklistGroup::class,
            'confirmed_at' => 'datetime',
        ];
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(Cycle::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItem::class, 'form_id')->orderBy('sort');
    }

    public function isLocked(): bool
    {
        return $this->status === FormStatus::Confirmed;
    }
}
