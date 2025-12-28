<?php

namespace Modules\Proposal\Livewire;

use Livewire\Component;
use Modules\Proposal\Models\Proposal;
use Modules\Proposal\Services\ProposalService;

class ProposalApprove extends Component
{
    public Proposal $proposal;
    public string $comment = '';

    public function approve(ProposalService $service)
    {
        $this->authorize('proposal.approve');

        $service->approve($this->proposal, auth()->id(), $this->comment);

        session()->flash('success', 'Đã duyệt đề xuất');
    }

    public function reject(ProposalService $service)
    {
        $this->authorize('proposal.reject');

        $this->validate([
            'comment' => 'required|string',
        ]);

        $service->reject($this->proposal, auth()->id(), $this->comment);

        session()->flash('success', 'Đã từ chối và gửi lại đề xuất');
    }
    public function render()
    {
        return view('Proposal::livewire.proposal-approve');
    }
}
