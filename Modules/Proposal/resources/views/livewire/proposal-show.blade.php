<div>
    <h4>{{ $proposal->title }}</h4>

    <p class="text-muted">
        Người tạo: {{ $proposal->creator->name ?? '-' }}
    </p>

    <p>{{ $proposal->description }}</p>

    <span class="badge badge-info">
        {{ $proposal->status }}
    </span>

    {{-- Approve / Reject --}}
    @canany(['proposal.approve','proposal.reject'])
        <div class="mt-4">
            @include('Proposal::livewire.proposal-approve')
        </div>
    @endcanany

    {{-- Comments --}}
    <div class="mt-4">
        @include('Proposal::livewire.proposal-comments')
    </div>

    {{-- Files --}}
    <div class="mt-4">
        @include('Proposal::livewire.proposal-files')
    </div>
</div>
