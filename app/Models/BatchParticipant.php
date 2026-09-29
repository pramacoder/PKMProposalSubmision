<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchParticipant extends Model
{
    protected $fillable = [
        'batch_id',
        'user_id',
        'role',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(DecisionBatch::class, 'batch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
