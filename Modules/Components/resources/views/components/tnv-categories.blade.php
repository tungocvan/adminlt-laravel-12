<div>
    {{-- @if ($label)
        <label class="form-label">{{ $label }}</label>
    @endif --}}

    <select 
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'form-control']) }}
    >
        <option value="">-- {{ $label }} --</option>

        @foreach ($categories as $c)
            <option value="{{ $c['id'] }}" 
                {{ $selected == $c['id'] ? 'selected' : '' }}>
                {{ $c['name'] }}
            </option>
        @endforeach
    </select>
</div>
