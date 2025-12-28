<?php

namespace Modules\Proposal\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Proposal\Models\Proposal;
use Modules\Proposal\Services\ProposalFileService;

class ProposalFileUpload extends Component
{
    use WithFileUploads;

    public Proposal $proposal;

    /** @var array<int, \Livewire\TemporaryUploadedFile> */
    public array $files = [];

    protected function rules(): array
    {
        return [
            'files.*' => 'file|max:10240', // 10MB per file
        ];
    }

    public function uploadFiles(ProposalFileService $service)
    {
        $this->validate();

        $service->upload($this->proposal, $this->files, auth()->id());

        $this->reset('files');

        session()->flash('success', 'Upload file thành công');
    }
    public function render()
    {
        return view('Proposal::livewire.proposal-file-upload');
    }
}
