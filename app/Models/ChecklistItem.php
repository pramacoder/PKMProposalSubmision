<?php

namespace App\Models;

use App\Enums\ChecklistItemKind;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    protected $fillable = ['form_id', 'code', 'label', 'kind', 'applies_to', 'sort'];

    protected function casts(): array
    {
        return [
            'kind' => ChecklistItemKind::class,
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(ChecklistForm::class, 'form_id');
    }

    public function isAuto(): bool
    {
        return $this->kind === ChecklistItemKind::Auto;
    }
}
