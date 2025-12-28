<?php

namespace Modules\Proposal\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalApproval extends Model
{
    protected $fillable = ['proposal_id', 'step_order', 'approver_id', 'status', 'acted_at'];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approver_id');
    }
}
