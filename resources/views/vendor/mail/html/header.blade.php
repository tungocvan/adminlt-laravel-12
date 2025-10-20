@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel' || trim($slot) === 'TNV')
<img src="https://adminlt.tungocvan.com/images/logo.png" class="logo" alt="Inafo Viet Nam Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
 