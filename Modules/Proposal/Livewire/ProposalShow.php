<?php

namespace Modules\Proposal\Livewire;

use Livewire\Component;
use Modules\Proposal\Models\Proposal;
use Modules\Proposal\Services\ProposalService;

class ProposalShow extends Component
{
    public Proposal $proposal;
    public string $comment = '';

    public function mount(Proposal $proposal)
    {
        $this->proposal = $proposal->load(['creator', 'comments.user', 'files', 'approvals']);
    }

    public function approve(ProposalService $service)
    {
        $service->approve($this->proposal, auth()->user(), $this->comment);

        $this->comment = '';
        session()->flash('success', 'Đã duyệt đề xuất');
    }

    public function reject(ProposalService $service)
    {
        $service->reject($this->proposal, auth()->user(), $this->comment);

        $this->comment = '';
        session()->flash('success', 'Đã từ chối đề xuất');
    }
    public function render()
    {
        return view('Proposal::livewire.proposal-show');
    }
}
