<?php

namespace App\Models;

use App\Enums\FundingSource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalFunding extends Model
{
    protected $fillable = ['proposal_id', 'source', 'amount'];

    protected function casts(): array
    {
        return [
            'source' => FundingSource::class,
            'amount' => 'integer',
        ];
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
