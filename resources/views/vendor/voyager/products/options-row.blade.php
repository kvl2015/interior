@if (isset($data['type']))
<h4>{{ $data['group']->name }} ({{ $data['group']->code }}) <a href="javascript:;" onclick="addOptionGroup('{{ $data['group']->id }}')">Add option</a></h4>
    <table class="table" id="group_{{ $data['groupId'] }}">
        <tr>
            <th>Article</th>
            <th>Name</th>
            <th>Price</th>
            <th>Discount</th>
            <th>Photo</th>
            <th>&nbsp;</th>
        </tr>
        <tbody>
            @foreach ($data['options'] as $selected)
                <tr>
                    <td>
                        <input type="text" name="article[{{ $selected->group_id }}][]" value="{{ $selected->code }}" />
                        <input type="hidden" name="ids[{{ $selected->group_id }}][]" value="{{ $selected->id }}" />
                    </td>
                    <td>{{ $selected->name }} </td>
                    <td><input type="text" name="oprice[{{ $selected->group_id }}][]" value="" class="form-control" style="width: 100px" /></td>
                    <td><input type="text" name="odiscount[{{ $selected->group_id }}][]" value="" class="form-control" style="width: 100px" /></td>
                    <td class="opt-photo">
                        <input type="file" name="optphoto[{{ $selected->group_id }}][]"><span class="opt-photo-thumb"></span>
                        <input type="hidden" name="optloadedphoto[{{ $selected->group_id }}][]" />
                    </td>
                    <td><a class="voyager-trash" href="javascript:;" onclick="$(this).parent().parent().remove()"></a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    @foreach ($data['options'] as $selected)
        @if (!in_array($selected->id, $data['exist']))
            <tr>
                <td>
                    <input type="text" name="article[{{ $selected->group_id }}][]" value="{{ $selected->code }}" />
                    <input type="hidden" name="ids[{{ $selected->group_id }}][]" value="{{ $selected->id }}" />
                </td>
                <td>{{ $selected->name }} </td>
                <td><input type="text" name="oprice[{{ $selected->group_id }}][]" value="" class="form-control" style="width: 100px" /></td>
                <td><input type="text" name="odiscount[{{ $selected->group_id }}][]" value="" class="form-control" style="width: 100px" /></td>
                <td class="opt-photo">
                    <input type="file" name="optphoto[{{ $selected->group_id }}][]"><span class="opt-photo-thumb"></span>
                    <input type="hidden" name="optloadedphoto[{{ $selected->group_id }}][]" />
                </td>
                <td><a class="voyager-trash" href="javascript:;" onclick="$(this).parent().parent().remove()"></a></td>
            </tr>
        @endif
    @endforeach
@endif
