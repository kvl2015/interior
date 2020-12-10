<div class="floating-label">
    <select name="{{ $name }}" id="{{ $name }}" class="floating-input" placeholder=" ">
        @if (isset($empty))
            <option value="">{{$empty}}</option>
        @endif
        @foreach($options as $key => $value)
            <option value="{{$key}}" {{$key == $selected ? 'selected' : ''}}>{{mb_strtolower($value)}}</option>
        @endforeach
    </select>

    <span class="highlight"></span>
    <label class="{{ $required ? 'required' : '' }}">{{ $label }}</label>
</div>
