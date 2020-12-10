<div class="floating-label">
    <textarea name="{{ $name }}" id="{{ $name }}" value="{{ $value }}" class="floating-input" placeholder=" ">{{ $value }}</textarea>
    <label class="{{ $required ? 'required' : '' }}">{{ $label }}</label>
</div>