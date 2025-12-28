<?php

namespace Modules\Proposal\Livewire;

use Livewire\Component;
use Modules\Proposal\Services\ProposalService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProposalCreate extends Component
{
    use AuthorizesRequests;

    public string $title = '';
    public string $description = '';
    public ?string $expected_time = null;
    public string $priority = 'MEDIUM';

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'expected_time' => 'nullable|date',
            'priority' => 'required|in:LOW,MEDIUM,HIGH',
        ];
    }

    public function submit(ProposalService $service)
    {
        $this->authorize('proposal-create');
        
        $this->validate();

        $service->create(
            [
                'title' => $this->title,
                'description' => $this->description,
                'expected_time' => $this->expected_time,
                'priority' => $this->priority,
            ],
            auth()->id(),
        );

        session()->flash('success', 'Đề xuất đã được gửi thành công');

        return redirect()->route('admin.proposals.index');
    }

    public function render()
    {
        return view('Proposal::livewire.proposal-create');
    }
}
