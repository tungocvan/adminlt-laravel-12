<tr>
    <td>{{ $cat->id }}</td>

    <td>
        {!! str_repeat('&nbsp;&nbsp;&nbsp;', $level) !!}
        @if($level > 0) └── @endif
        {{ $cat->name }}
    </td>

    <td>{{ $cat->type }}</td>

    <td>{{ $cat->parent->name ?? '—' }}</td>
</tr>

@if($cat->children)
    @foreach($cat->children as $child)
        @include('livewire.category.category-row', [
            'cat' => $child,
            'level' => $level + 1
        ])
    @endforeach
@endif
