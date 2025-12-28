<?php
// MODULE: Proposal
// STEP 3: SERVICE & WORKFLOW LOGIC
// Laravel 12 | Livewire 3.1 | Spatie Permission

namespace Modules\Proposal\Services;

use Illuminate\Support\Facades\DB;
use Modules\Proposal\Models\{
    Proposal,
    ProposalApproval,
    ProposalWorkflow,
    ProposalComment,
    ProposalFile
};

class ProposalService
{
    /* ==============================
     | STATUS CONSTANTS
     ============================== */

    public const STATUS_PENDING   = 'PENDING';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_CANCELLED = 'CANCELLED';

    /* ==============================
     | CREATE PROPOSAL
     ============================== */

    public function create(array $data, int $userId): Proposal
    {
        
        return DB::transaction(function () use ($data, $userId) {
            //dd($data,$userId); có dữ liệu $data và $userId
            $proposal = Proposal::create([
                'title'       => $data['title'],
                'description' => $data['description'],
                'expected_time' => $data['expected_time'] ?? null,
                'priority'    => $data['priority'] ?? 'MEDIUM',
                'status'      => self::STATUS_PENDING,
                'created_by'  => $userId,
            ]);
    
            $workflow = ProposalWorkflow::where('is_active', true)->first();
            if (!$workflow) {
                throw new \Exception('Chưa cấu hình workflow duyệt');
            }
            // 3. Lấy các step của workflow
            $steps = $workflow->steps()
                ->orderBy('step_order')
                ->get();

            if ($steps->isEmpty()) {
                throw new \RuntimeException('Workflow chưa có bước duyệt');
            }
            
        // 4. Tạo các approval step
            foreach ($workflow->steps as $step) {
                ProposalApproval::create([
                    'proposal_id' => $proposal->id,
                    'step_order'  => $step->step_order,
                    'status'      => 'PENDING',
                ]);
            }

            return $proposal;
        });
    }

    /* ==============================
     | APPROVE STEP
     ============================== */

    public function approve(Proposal $proposal, int $userId, ?string $comment = null): void
    {
        DB::transaction(function () use ($proposal, $userId, $comment) {
            $approval = $proposal->approvals()
                ->where('status', 'PENDING')
                ->orderBy('step_order')
                ->firstOrFail();

            $approval->update([
                'status'     => 'APPROVED',
                'approver_id'=> $userId,
                'acted_at'   => now(),
            ]);

            if ($comment) {
                ProposalComment::create([
                    'proposal_id' => $proposal->id,
                    'user_id'     => $userId,
                    'comment'     => $comment,
                ]);
            }

            $hasNext = $proposal->approvals()
                ->where('status', 'PENDING')
                ->exists();

            if (! $hasNext) {
                $proposal->update(['status' => self::STATUS_COMPLETED]);
            }
        });
    }

    /* ==============================
     | REJECT PROPOSAL
     ============================== */

    public function reject(Proposal $proposal, int $userId, string $comment): void
    {
        DB::transaction(function () use ($proposal, $userId, $comment) {
            $approval = $proposal->approvals()
                ->where('status', 'PENDING')
                ->orderBy('step_order')
                ->firstOrFail();

            $approval->update([
                'status'      => 'REJECTED',
                'approver_id' => $userId,
                'acted_at'    => now(),
            ]);

            ProposalComment::create([
                'proposal_id' => $proposal->id,
                'user_id'     => $userId,
                'comment'     => $comment,
            ]);

            // Reset workflow: send back to step 1
            $proposal->approvals()->where('status', '!=', 'PENDING')->update([
                'status' => 'PENDING',
                'approver_id' => null,
                'acted_at' => null,
            ]);
        });
    }

    /* ==============================
     | CANCEL BY EMPLOYEE
     ============================== */

    public function cancel(Proposal $proposal, int $userId): void
    {
        if ($proposal->created_by !== $userId) {
            abort(403);
        }

        if ($proposal->status !== self::STATUS_PENDING) {
            abort(400, 'Proposal cannot be cancelled');
        }

        $proposal->update(['status' => self::STATUS_CANCELLED]);
    }
}
