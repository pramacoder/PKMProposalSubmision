<?php

namespace App\Models;

use App\Enums\DocumentStage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalFile extends Model
{
    protected $fillable = [
        'proposal_id', 'requirement_id', 'stage', 'version',
        'path', 'original_name', 'size', 'mime', 'checksum',
        'uploaded_by', 'is_current',
    ];

    protected function casts(): array
    {
        return [
            'stage' => DocumentStage::class,
            'version' => 'integer',
            'size' => 'integer',
            'is_current' => 'boolean',
        ];
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(DocumentRequirement::class, 'requirement_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
