<table class="table">
    <tr>
        <th>&nbsp;</th>
        <th>Code</th>
        <th>Value</th>
    </tr>
    @foreach ($data['options'] as $selected)
        <tr style="background: {{ in_array($selected->id, $data['selected']) ? '#f1f1f1' : '#fff' }}">
            <td><input type="checkbox" name="ids[]" value="{{ $selected->id }}" {{ in_array($selected->id, $data['selected']) ? "checked disabled" : "" }}></td>
            <td>{{ $selected->code }}</td>
            <td>{{ $selected->name }} </td>
        </tr>
    @endforeach
    <input type="hidden" name="groupId" id="groupId" value="{{ $data['groupId'] }}">
</table>
