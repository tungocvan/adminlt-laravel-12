<tr wire:sortable.item="{{ $cat->id }}">
    <td><input type="checkbox" wire:model="selected" value="{{ $cat->id }}"></td>
    <td>::</td>
    <td>{{ $prefix }} {{ $cat->term->name }}</td>
    <td>{{ $cat->term->slug }}</td>
    <td>{{ $cat->parent ? $cat->parent->term->name : '-' }}</td>
    <td>
        <button wire:click="edit({{ $cat->id }})" class="btn btn-sm btn-info">Sửa</button>
        <button wire:click="delete({{ $cat->id }})" class="btn btn-sm btn-danger">Xóa</button>
    </td>
</tr>

@foreach ($cat->children as $child)
    @include('livewire.admin.partials.category-row', ['cat' => $child, 'prefix' => $prefix . '— '])
@endforeach