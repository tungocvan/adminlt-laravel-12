@php
    $indent = str_repeat('— ', $level);
@endphp

<tr>
    <td>
        <input type="checkbox" wire:model="selectedCategories" value="{{ $item['id'] }}">
    </td>

    <td>{{ $item['id'] }}</td>

    <td>
        <strong>{!! $indent !!}</strong> {{ $item['name'] }}
    </td>

    <td>{{ $item['slug'] }}</td>

    <td>{{ $item['type'] }}</td>

    <td>{{ $item['parent_name'] ?? '-' }}</td>

    <td width="160">
        <button class="btn btn-sm btn-primary" wire:click="edit({{ $item['id'] }})">
            Sửa
        </button>

        <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $item['id'] }})">
            Xóa
        </button>
    </td>
</tr>

{{-- Render con nếu có --}}
@if (!empty($item['children']))
    @foreach ($item['children'] as $child)
        @include('livewire.category.parts.tree-row', [
            'item' => $child,
            'level' => $level + 1  {{-- Tăng level cho con --}}
        ])
    @endforeach
@endif
