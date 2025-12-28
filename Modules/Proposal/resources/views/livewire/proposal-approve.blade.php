<div class="card">
    <div class="card-header">Duyệt đề xuất</div>
    <div class="card-body">

        <div class="form-group">
            <label>Ý kiến</label>
            <textarea class="form-control" wire:model.defer="comment"></textarea>
            @error('comment')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button wire:click="approve" class="btn btn-primary">Approve</button>
        <button wire:click="reject" class="btn btn-danger">Reject</button>
    </div>
</div>
