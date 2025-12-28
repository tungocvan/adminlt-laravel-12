<?php

namespace Modules\Proposal\Livewire;

use Livewire\Component;
use Modules\Proposal\Models\Proposal;

class ProposalIndex extends Component
{
    public function render()
    {
        $user = auth()->user();

        $proposals = $user->can('proposal.view.all') ? Proposal::latest()->get() : Proposal::where('created_by', $user->id)->latest()->get();
        $proposals = Proposal::latest()->get();
        return view('Proposal::livewire.proposal-index',compact('proposals'));
    }
}
