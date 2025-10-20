@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="/images/logo.png" class="logo" alt="Inafo Viet Nam Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
 