<div 
    x-data="moneyInput(@entangle($attributes->wire('model')).live)"
    class="form-group"
>
    @if($label ?? false)
        <label>{{ $label }}</label>
    @endif

    <input type="text" 
           class="form-control" 
           x-model="display"
           x-on:input="formatInput($event)"
           x-on:blur="formatBlur($event)">

    <small class="text-muted" x-show="value">
        <span x-text="formatDisplay(value)"></span> đ
    </small>
</div>
