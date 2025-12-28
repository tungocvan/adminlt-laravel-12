<?php

namespace Modules\Proposal\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Proposal\Models\ProposalWorkflow;
class ProposalWorkflowStep extends Model
{
    protected $fillable = ['workflow_id', 'step_order', 'role_name'];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(ProposalWorkflow::class, 'workflow_id');
    }
}
