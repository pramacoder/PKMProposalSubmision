<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequirement extends Model
{
    protected $fillable = [
        'scheme_id', 'code', 'label', 'is_required',
        'sort', 'allowed_mimes', 'max_kb',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'sort' => 'integer',
            'max_kb' => 'integer',
        ];
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(Scheme::class);
    }
}
