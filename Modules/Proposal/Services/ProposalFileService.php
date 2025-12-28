<?php
// MODULE: Proposal
// STEP 5: FILE UPLOAD (MULTI FILE - LOCAL STORAGE)
// Laravel 12 | Livewire 3.1

/* =====================================================
| CONFIG NOTE
| storage/app/proposals/{proposal_id}/
===================================================== */

/* =====================================================
| MODEL UPDATE: ProposalFile (no change needed)
===================================================== */

/* =====================================================
| SERVICE: handle upload
===================================================== */

namespace Modules\Proposal\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Proposal\Models\Proposal;
use Modules\Proposal\Models\ProposalFile;

class ProposalFileService
{
    public function upload(Proposal $proposal, array $files, int $userId): void
    {
        foreach ($files as $file) {
            /** @var UploadedFile $file */

            $path = $file->store('proposals/' . $proposal->id, 'local');

            ProposalFile::create([
                'proposal_id' => $proposal->id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'uploaded_by' => $userId,
            ]);
        }
    }
}
