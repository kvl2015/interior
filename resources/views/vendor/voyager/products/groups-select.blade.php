<select class="form-control" name="groups" id="groups">
    <option value="">Select group</option>
    @foreach($data['groups'] as $group)
        <option value={{ $group->id }}>{{ $group->name }} ({{ $group->code }})</option>
    @endforeach
</select>