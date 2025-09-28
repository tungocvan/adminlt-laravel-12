@props([
    'field',
    'label',
    'sortField' => null,
    'sortDirection' => 'asc',
])

<th 
    scope="col" 
    class="cursor-pointer align-middle"
    wire:click="sortBy(@js($field))"
>
    {{ $label }}

    {{-- Icon sort --}}
    @if ($sortField === $field)
           
        @if ($sortDirection === 'asc')
       
        <i class="fa fa-sort-up text-primary ml-1"></i>
        @else
        <i class="fa fa-sort-down text-primary ml-1"></i>
        @endif
    @else
  
        <i class="fa fa-sort text-mute ml-1"></i>
    @endif
</th>
