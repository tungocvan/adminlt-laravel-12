<div>
    {{-- @if ($label)
        <label class="form-label">{{ $label }}</label>
    @endif --}}

    <select 
        wire:model.live="{{ $model }}"
        class="form-control"
    >
        <option value="">-- {{ $label }} --</option>

        @foreach ($parents as $p)
            <option value="{{ $p['id'] }}">
                {{ $p['name'] }}
            </option>
        @endforeach
    </select>
</div>
