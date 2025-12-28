<?php

namespace Modules\Proposal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/* ==============================
| MODEL: Proposal
============================== */

class Proposal extends Model
{
    protected $fillable = ['title', 'description', 'expected_time', 'priority', 'status', 'created_by'];

    protected $casts = [
        'expected_time' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ProposalApproval::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProposalComment::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProposalFile::class);
    }
}
