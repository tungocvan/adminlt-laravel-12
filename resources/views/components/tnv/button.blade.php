@props(['label' => 'Submit' ,'href','quanlity', 'theme'=>'success'])


@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "btn btn-app bg-{$theme}"]) }}>
    @isset($quanlity)<span class="badge bg-purple">{{ $quanlity }}</span>@endisset
    @isset($icon)<i class="{{ $icon }}"></i>@endisset {{ $label }}
    </a>
@else

<button type="{{ $type }}" {{ $attributes->merge(['class' => "btn btn-{$theme}"]) }}  @isset($name) $name @endisset @isset($id) $id @endisset @isset($value) $value @endisset >
    @isset($icon)<i class="{{ $icon }}"></i>@endisset {{ $label }}
</button>

@endif

