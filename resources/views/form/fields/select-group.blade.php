<div class="floating-label">
    @php // dd($data['category']) @endphp
    <select name="{{ $name }}" id="{{ $name }}" class="floating-input" placeholder=" ">
        <option value="">Оберіть категорію</option>
        @foreach($options as $key => $item)
            <option value="{{$key}}" {{$key == $selected ? 'selected' : ''}}>{{mb_strtolower($item['parent'][1])}}</option>
            @foreach($item['childs'] as $ckey => $child)
                <option value="{{$ckey}}" {{$ckey == $selected ? 'selected' : ''}}>&nbsp;&nbsp;&nbsp;&nbsp;{{mb_strtolower($child[1])}}</option>
            @endforeach
        @endforeach
    </select>

    <span class="highlight"></span>
    <label class="{{ $required ? 'required' : '' }}">{{ $label }}</label>
</div>
