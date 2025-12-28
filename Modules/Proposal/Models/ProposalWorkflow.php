<?php

namespace Modules\Proposal\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Proposal\Models\ProposalWorkflowStep;
class ProposalWorkflow extends Model
{
    protected $fillable = ['name', 'is_active'];

    public function steps(): HasMany
    {
        return $this->hasMany(ProposalWorkflowStep::class, 'workflow_id')->orderBy('step_order');
    }
}
