<?php
// MODULE: Proposal
// STEP 2: MODELS & RELATIONSHIPS
// Laravel 12 | Livewire 3.1 | Spatie Permission

namespace Modules\Proposal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/* ==============================
 | MODEL: Proposal
 ============================== */

class Proposal extends Model
{
    protected $fillable = [
        'title',
        'description',
        'expected_time',
        'priority',
        'status',
        'created_by',
    ];

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

/* ==============================
 | MODEL: ProposalWorkflow
 ============================== */

class ProposalWorkflow extends Model
{
    protected $fillable = ['name', 'is_active'];

    public function steps(): HasMany
    {
        return $this->hasMany(ProposalWorkflowStep::class, 'workflow_id')
            ->orderBy('step_order');
    }
}

/* ==============================
 | MODEL: ProposalWorkflowStep
 ============================== */

class ProposalWorkflowStep extends Model
{
    protected $fillable = [
        'workflow_id',
        'step_order',
        'role_name',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(ProposalWorkflow::class, 'workflow_id');
    }
}

/* ==============================
 | MODEL: ProposalApproval
 ============================== */

class ProposalApproval extends Model
{
    protected $fillable = [
        'proposal_id',
        'step_order',
        'approver_id',
        'status',
        'acted_at',
    ];

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

/* ==============================
 | MODEL: ProposalComment
 ============================== */

class ProposalComment extends Model
{
    protected $fillable = [
        'proposal_id',
        'user_id',
        'comment',
    ];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

/* ==============================
 | MODEL: ProposalFile
 ============================== */

class ProposalFile extends Model
{
    protected $fillable = [
        'proposal_id',
        'file_path',
        'file_name',
        'uploaded_by',
    ];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }
}
