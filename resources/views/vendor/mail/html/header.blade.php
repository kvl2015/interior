<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="http://selectinterior.world/images/logo.png" class="logo" alt="Select Interior Shop">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
